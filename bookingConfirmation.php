<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); 

require 'includes/db_connect.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $customerID = isset($_GET['customerID']) ? (int)$_GET['customerID'] : 1;
    $seatCount = isset($_GET['seatCount']) ? (int)$_GET['seatCount'] : 3;

    if ($customerID <= 0 || $seatCount <= 0) {
        throw new Exception('Invalid customer ID or seat count.');
    }

    $stmt = $pdo->prepare("
        SELECT b.bookingID, b.seatID, b.Price, b.datetimeID, b.movieID,
               c.fullName, c.email,
               m.movieName,
               d.date, d.time
        FROM booking b
        JOIN customer c ON b.customerID = c.customerID
        JOIN movie m ON b.movieID = m.movieID
        JOIN datetime d ON b.datetimeID = d.datetimeID
        WHERE b.customerID = ?
        ORDER BY b.bookingID DESC
        LIMIT ?
    ");
    
    $stmt->bindValue(1, $customerID, PDO::PARAM_INT);
    $stmt->bindValue(2, $seatCount, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($bookings) || count($bookings) != $seatCount) {
        error_log("bookingConfirmation.php: Insufficient or no bookings found for customerID: $customerID, expected seatCount: $seatCount");
        throw new Exception('No bookings found for the provided customer and seat count.');
    }

    $customerName = $bookings[0]['fullName'];
    $customerEmail = $bookings[0]['email'];
    $movieName = $bookings[0]['movieName'];
    $date = $bookings[0]['date'];
    $time = substr($bookings[0]['time'], 0, 5);
    $pricePerSeat = (float)$bookings[0]['Price'];
    $totalPrice = $pricePerSeat * $seatCount;
    $seatNumbers = implode(', ', array_column($bookings, 'seatID'));

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'minsittmandalay137@gmail.com'; // REPLACE THIS with your Gmail address
        $mail->Password = 'dpvtkoiyrjgjnrql'; // REPLACE THIS with your 16-character App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('minsittmandalay137@gmail.com', 'Cinema Team');
        $mail->addAddress($customerEmail, $customerName);

        $mail->isHTML(false);
        $mail->Subject = 'Your Booking Confirmation';
        $mail->Body = "Dear $customerName,\n\nThank you for your booking. Below are the details:\n\n" .
                      "Customer Name: $customerName\n" .
                      "Email: $customerEmail\n" .
                      "Movie: $movieName\n" .
                      "Date: $date\n" .
                      "Time: $time\n" .
                      "Price per Seat: $pricePerSeat B\n" .
                      "Total Price: $totalPrice B\n" .
                      "Seat Numbers: $seatNumbers\n\n" .
                      "We look forward to seeing you!\n\nBest regards,\nCinema Team";

        $mail->send();
        error_log("bookingConfirmation.php: Successfully sent booking confirmation email to $customerEmail for customerID: $customerID");
        
        echo "<script>alert('Booking confirmation sent to your email!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        error_log("bookingConfirmation.php: PHPMailer Error: " . $mail->ErrorInfo);
        echo "<script>alert('Failed to send booking confirmation email. Error: " . addslashes($mail->ErrorInfo) . "'); window.location.href='index.php';</script>";
    }
    error_log("bookingConfirmation.php: Successfully retrieved $seatCount booking details for customerID: $customerID, seatIDs: " . json_encode(array_column($bookings, 'seatID')));

} catch (Exception $e) {
    error_log("bookingConfirmation.php: Error: " . $e->getMessage());
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location.href='index.php';</script>";
}
?>