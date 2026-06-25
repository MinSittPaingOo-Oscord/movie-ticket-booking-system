<?php
header('Content-Type: application/json');
require 'includes/db_connect.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $movieID = isset($input['movieID']) ? (int)$input['movieID'] : 0;
    $datetimeID = isset($input['datetimeID']) ? (int)$input['datetimeID'] : 0;

    if ($movieID <= 0 || $datetimeID <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Get booked seats
    $stmt = $pdo->prepare("
        SELECT seatID
        FROM booking
        WHERE movieID = :movieID AND datetimeID = :datetimeID
    ");
    $stmt->execute(['movieID' => $movieID, 'datetimeID' => $datetimeID]);
    $bookedSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($bookedSeats);
} catch (Exception $e) {
    error_log("Error fetching booked seats: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>