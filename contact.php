<?php
require 'includes/db_connect.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set a log file for mail errors
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xamppp\htdocs\PHP\DSA Project\mail_errors.log');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize form inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $message = sanitizeInput($_POST['message']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('All fields are required!'); window.location.href='contact.php';</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href='contact.php';</script>";
        exit;
    }

    // PHPMailer setup
    $mail = new PHPMailer(true);
    try {
        // Enable verbose debug output
        $mail->SMTPDebug = 2; // 2 for detailed debug, 0 to disable in production
        $mail->Debugoutput = function($str, $level) {
            file_put_contents('C:\xamppp\htdocs\PHP\DSA Project\mail_debug.log', gmdate('Y-m-d H:i:s') . "\t$level\t$str\n", FILE_APPEND);
        };

        // Server settings (try port 587 with TLS first)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'minsittmandalay137@gmail.com'; // REPLACE THIS with your Gmail address (e.g., john.doe@gmail.com)
        $mail->Password = 'dpvtkoiyrjgjnrql'; // REPLACE THIS with your 16-character App Password (e.g., abcdefghijklmnop, no spaces)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('minsittmandalay137@gmail.com', 'Your Website'); // REPLACE THIS with your Gmail address (same as Username)
        $mail->addAddress('minsittmandalay137@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Contact Form Submission from ' . $name;
        $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        $mail->send();
        echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        try {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->send();
            echo "<script>alert('Message sent successfully using port 465!'); window.location.href='index.php';</script>";
        } catch (Exception $e2) {
            error_log("PHPMailer Error (Port 465): " . $mail->ErrorInfo);
            echo "<script>alert('Failed to send message. Error: " . addslashes($mail->ErrorInfo) . "'); window.location.href='index.php';</script>";
        }
    }
}
?>