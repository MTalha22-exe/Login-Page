<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();


if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}


$host = "localhost";
$user = "root";
$pass = "";
$db = "login_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) { echo "User ID missing."; exit; }

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Error deleting user.";
}
$stmt->close();
?>
