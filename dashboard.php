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
  <hr>



</div>
<?php if (isset($_GET['success'])): ?>
  <div class="message success">✅ Your message was sent successfully!</div>
<?php elseif (isset($_GET['error'])): ?>
  <div class="message error">❌ Sorry, there was an error sending your message.</div>
<?php endif; ?>


<footer class="contact-footer">
  <h3>Contact Us</h3>
  <form action="sendmail.php" method="post" class="contact-form">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <textarea name="message" placeholder="Your Message" required></textarea>
    <button type="submit">Send Message</button>
  </form>
</footer>
</body>
</html>
