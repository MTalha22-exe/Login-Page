<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "login_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user matching both username and email
    $stmt = $conn->prepare("SELECT id, password, role, email FROM users WHERE username=? AND email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role, $user_email);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['userid'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $user_email;  // save email in session

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid username or email!";
    }
    $stmt->close();
}
?>




<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
 

</head>

<body>
  <div class="container">
    <h2>Login</h2>
    <?php if ($error): ?>
      <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
       
    <form method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>

    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    <p><a href="forgot_password.php">Forgot Password?</a></p>
  </div>
</body>

</html>