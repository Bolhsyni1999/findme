<?php
// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form fields and remove whitespace.
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Check if data is valid
    if (empty($name) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set a 400 (bad request) response code and exit.
        http_response_code(400);
        echo "Please complete the form and try again.";
        exit;
    }

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';            // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = '844d716ae47c66';  // Replace with your SMTP username
        $mail->Password = 'fdc86c68d58c9b';        // Replace with your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use `PHPMailer::ENCRYPTION_SMTPS` for SSL
        $mail->Port = 587;                              // Replace with your SMTP port (587 for TLS, 465 for SSL)


		// Enable SMTP debugging
		$mail->SMTPDebug = 2;

        // Set email content
        $mail->setFrom($email, $name);
        $mail->addAddress('recipient-email@findme.ma'); // Replace with recipient's email address
        $mail->Subject = "New Contact from $name: $subject";
        $mail->Body = "Name: $name\nEmail: $email\n\nSubject: $subject\n\nMessage:\n$message";

        // Send the email
        $mail->send();
        
        // Set a 200 (okay) response code.
        http_response_code(200);
        echo "Thank You! Your message has been sent.";

    } catch (Exception $e) {
        // Set a 500 (internal server error) response code.
        http_response_code(500);
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    // Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
