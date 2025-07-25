<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Forgot Password</h2>
  <form action="send_reset_code.php" method="post">
    <input type="text" name="username" placeholder="Enter your username" required><br>
    <input type="email" name="email" placeholder="Enter your email" required><br>
    <button type="submit">Send Reset Code</button>
</form>

  <p><a href="index.php">Back to Login</a></p>
</div>
</body>
</html>
