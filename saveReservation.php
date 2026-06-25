<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); 

header('Content-Type: application/json');
require 'includes/db_connect.php';

try {
    $movieID = isset($_POST['movieID']) ? (int)$_POST['movieID'] : 0;
    $customerID = isset($_POST['customerID']) ? (int)$_POST['customerID'] : 0;
    $datetimeID = isset($_POST['datetimeID']) ? (int)$_POST['datetimeID'] : 0;
    $seatIDs = isset($_POST['seats']) && is_array($_POST['seats']) ? array_map('intval', $_POST['seats']) : [];
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $count = isset($_POST['count']) ? (int)$_POST['count'] : 0;

    if (empty($seatIDs)) {
        throw new Exception('No seats selected.');
    }

    if ($movieID <= 0) {
        throw new Exception('Invalid movie selection.');
    }

    if ($customerID !== 1) {
        throw new Exception('Invalid customer ID.');
    }

    if ($datetimeID <= 0) {
        throw new Exception('Invalid datetime selection.');
    }

    if ($count !== count($seatIDs)) {
        throw new Exception('Mismatch between selected seats and ticket count.');
    }

    if ($price <= 0) {
        throw new Exception('Invalid price.');
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT customerID FROM customer WHERE customerID = :customerID");
    $stmt->execute(['customerID' => $customerID]);
    if (!$stmt->fetchColumn()) {
        $pdo->rollBack();
        throw new Exception('Invalid customer ID.');
    }

    $stmt = $pdo->prepare("SELECT moviexdatetime FROM moviexdatetime WHERE movieID = :movieID AND datetimeID = :datetimeID");
    $stmt->execute(['movieID' => $movieID, 'datetimeID' => $datetimeID]);
    $moviexdatetimeID = $stmt->fetchColumn();

    if (!$moviexdatetimeID) {
        $pdo->rollBack();
        throw new Exception('Invalid movie or showtime.');
    }

    $stmt = $pdo->prepare("SELECT price FROM movie WHERE movieID = :movieID");
    $stmt->execute(['movieID' => $movieID]);
    $moviePrice = (float)$stmt->fetchColumn();

    if ($price != $moviePrice) {
        $pdo->rollBack();
        throw new Exception('Invalid price for selected movie. Expected: ' . $moviePrice);
    }

    $placeholders = implode(',', array_fill(0, count($seatIDs), '?'));
    $stmt = $pdo->prepare("
        SELECT seatID
        FROM booking
        WHERE seatID IN ($placeholders) AND movieID = ? AND datetimeID = ?
    ");
    $stmt->execute(array_merge($seatIDs, [$movieID, $datetimeID]));
    $bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($bookedSeats)) {
        $pdo->rollBack();
        throw new Exception('Some seats are already booked for this showtime: ' . implode(', ', $bookedSeats));
    }

    $stmt = $pdo->prepare("
        INSERT INTO booking (bookingStatus, customerID, movieID, seatID, Price, datetimeID)
        VALUES (:bookingStatus, :customerID, :movieID, :seatID, :Price, :datetimeID)
    ");
    
    foreach ($seatIDs as $seatID) {
        $stmt->execute([
            'bookingStatus' => 'allowed',
            'customerID' => $customerID,
            'movieID' => $movieID,
            'seatID' => $seatID,
            'Price' => $price,
            'datetimeID' => $datetimeID
        ]);
    }

    $pdo->commit();
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Booking successfully saved to database.'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Booking error: " . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
}
?>