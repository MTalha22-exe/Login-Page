<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // Mailtrap SMTP config
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'a06b0719167a13';
        $mail->Password   = 'cad8f456a74f01';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 2525;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('your@email.com', 'Your Name'); // where you receive message

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Contact form message';
        $mail->Body    = "<b>From:</b> $name <$email><br><b>Message:</b><br>$message";

        $mail->send();
        header("Location: dashboard.php?success=1");
        exit;
    } catch (Exception $e) {
        header("Location: dashboard.php?error=1");
        exit;
    }
} else {
    echo "Invalid request.";
}
