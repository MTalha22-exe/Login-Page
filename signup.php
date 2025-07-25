<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "login_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $email = trim($_POST['email']);

    // Check if username already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already taken!";
    } else {
        // insert new user with email
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $role, $email);
        if ($stmt->execute()) {
            $success = "Signup successful!";
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Sign Up</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Sign Up</h2>

  <?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if ($success): ?>
    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

 <form method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <label for="role">Select Role:</label>
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="employee">Employee</option>
    </select><br>
    <button type="submit">Sign Up</button>
</form>

  <p>Already have an account? <a href="index.php">Login here</a></p>
</div>
</body>
</html>
