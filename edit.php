<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Only allow admin
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "login_db";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) { echo "User ID missing."; exit; }

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $role, $id);

    try {
        if ($stmt->execute()) {
            $message = "User updated successfully!";
        } else {
            // Check for duplicate error (MySQL error code 1062)
            if ($conn->errno == 1062) {
                $message = "Error: Username already exists!";
            } else {
                $message = "Error updating user.";
            }
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $message = "Error: Username already exists!";
        } else {
            $message = "Database error: " . $e->getMessage();
        }
    }

    $stmt->close();
}


// Fetch user data
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) { echo "User not found."; exit; }
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit User</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Edit User</h2>
  <?php if($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <form method="post">
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>
    <select name="role" required>
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
        <option value="employee" <?= $user['role']=='employee'?'selected':'' ?>>Employee</option>
    </select><br>
    <button type="submit">Update</button>
  </form>
  <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
