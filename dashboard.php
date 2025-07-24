<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! 🎉</h2>
  <p>This is your dashboard.</p>
  <a href="logout.php">Logout</a>
</div>
</body>
</html>
