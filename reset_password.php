<?php
$username = $_GET['username'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Reset Password</h2>
  <form action="do_reset_password.php" method="post">
    <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
    <input type="text" name="code" placeholder="Enter received code" required><br>
    <input type="password" name="new_password" placeholder="New Password" required><br>
    <button type="submit">Reset Password</button>
  </form>
</div>
</body>
</html>
