<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}

// database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "login_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get filter and search
$filter_role = $_GET['filter_role'] ?? '';
$search_username = $_GET['search_username'] ?? '';

$users = [];
if ($_SESSION['role'] === 'admin') {
    if ($filter_role && $search_username) {
        $like = "%" . $search_username . "%";
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE role = ? AND username LIKE ?");
        $stmt->bind_param("ss", $filter_role, $like);
    } elseif ($filter_role) {
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE role = ?");
        $stmt->bind_param("s", $filter_role);
    } elseif ($search_username) {
        $like = "%" . $search_username . "%";
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username LIKE ?");
        $stmt->bind_param("s", $like);
    } else {
        $stmt = $conn->prepare("SELECT id, username, role FROM users");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // employees only see themselves
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['userid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<style>
  .filter-form {
    margin-bottom: 20px;
  }
  .search-box {
    width: 150px;
    padding: 4px;
    font-size: 14px;
    margin-top: 5px;
  }
</style>
</head>
<body>
<div class="container">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! 🎉</h2>
  <p>Role: <?= htmlspecialchars($_SESSION['role']) ?></p>
  <a href="logout.php">Logout</a>
  <hr>

<?php if ($_SESSION['role'] === 'admin'): ?>

  <form method="get" action="dashboard.php" class="filter-form">
    <label for="filter_role">Filter by Role:</label>
    <select name="filter_role" id="filter_role">
        <option value="">-- All --</option>
        <option value="admin" <?= ($filter_role === 'admin') ? 'selected' : '' ?>>Admin</option>
        <option value="employee" <?= ($filter_role === 'employee') ? 'selected' : '' ?>>Employee</option>
    </select>
    <br>
    <label for="search_username">Search Username:</label>
    <input type="text" name="search_username" id="search_username" class="search-box" value="<?= htmlspecialchars($search_username) ?>">
    <button type="submit">Apply</button>
    <a href="dashboard.php" style="margin-left:10px;">Reset</a>
  </form>

  <h3>All Users:</h3>
  <table border="1" cellpadding="8">
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
    <?php foreach($users as $user): ?>
    <tr>
      <td><?= htmlspecialchars($user['id']) ?></td>
      <td><?= htmlspecialchars($user['username']) ?></td>
      <td><?= htmlspecialchars($user['role']) ?></td>
      <td>
        <a href="edit.php?id=<?= $user['id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>

<?php else: ?>
  <h3>Your Details:</h3>
  <table border="1" cellpadding="8">
    <?php foreach($users as $user): ?>
    <tr>
      <td>ID</td>
      <td><?= htmlspecialchars($user['id']) ?></td>
    </tr>
    <tr>
      <td>Username</td>
      <td><?= htmlspecialchars($user['username']) ?></td>
    </tr>
    <tr>
      <td>Role</td>
      <td><?= htmlspecialchars($user['role']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
</div>
</body>
</html>
