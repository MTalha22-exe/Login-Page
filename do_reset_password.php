<?php
$conn = new mysqli("localhost", "root", "", "login_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$username = $_POST['username'];
$code = $_POST['code'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE username=? AND reset_code=?");
$stmt->bind_param("ss", $username, $code);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $update = $conn->prepare("UPDATE users SET password=?, reset_code=NULL WHERE username=?");
    $update->bind_param("ss", $new_password, $username);
    $update->execute();
    echo "Password reset successful! <a href='index.php'>Login now</a>";
} else {
    echo "Invalid code!";
}
?>
