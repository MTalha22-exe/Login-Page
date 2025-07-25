<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$conn = new mysqli("localhost", "root", "", "login_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// get data safely
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($username) || empty($email)) {
    die("Username and email are required!");
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username=? AND email=?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $code = rand(100000, 999999);

    $update = $conn->prepare("UPDATE users SET reset_code=? WHERE username=? AND email=?");
    $update->bind_param("sss", $code, $username, $email);
    $update->execute();

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = 'e9163ac995a8bc';
        $mail->Password = '23fb8bdbbb5688';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;

        $mail->setFrom('noreply@yourapp.com', 'Your App');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Code';
        $mail->Body    = "Your reset code is: <b>$code</b>";

        $mail->send();
        header("Location: reset_password.php?username=" . urlencode($username));
        exit;
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No account found with this username and email!";
}
?>
