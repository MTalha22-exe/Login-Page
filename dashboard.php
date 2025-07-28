<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "login_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page -1)*$limit;

// total users
$total_res = $conn->query("SELECT COUNT(*) as c FROM users");
$total = $total_res->fetch_assoc()['c'];
$total_pages = ceil($total / $limit);

// get first page users
$stmt = $conn->prepare("SELECT id, username, role FROM users LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! 🎉</h2>
  <p>Role: <?= htmlspecialchars($_SESSION['role']) ?></p>
  <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
  <hr>

<?php if ($_SESSION['role'] === 'admin'): ?>
  <!-- Filter + Search -->
  <div class="mb-3 d-flex gap-2">
    <select id="filterSelect" class="form-select w-auto">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="employee">Employee</option>
    </select>
    <input type="text" id="searchBox" placeholder="Search username..." class="form-control w-auto">
  </div>

  <!-- Users Table -->
  <table id="usersTable" class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user['id']) ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td>
          <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="delete.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Pagination -->
    <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
      </li>

      <?php for($i=1;$i<=$total_pages;$i++): ?>
        <li class="page-item <?= $i==$page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
      </li>
    </ul>
  </nav>

<?php else: ?>
  <h3>Your Details:</h3>
  <table class="table table-bordered">
    <tr><td>ID</td><td><?= htmlspecialchars($_SESSION['userid']) ?></td></tr>
    <tr><td>Username</td><td><?= htmlspecialchars($_SESSION['username']) ?></td></tr>
    <tr><td>Role</td><td><?= htmlspecialchars($_SESSION['role']) ?></td></tr>
  </table>
<?php endif; ?>
</div>

<!-- Bootstrap JS & optional Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AJAX Filter + Search -->
<script>
function applyFilter(){
    const filter = document.getElementById('filterSelect').value;
    const search = document.getElementById('searchBox').value;
    fetch(`fetch_users.php?filter=${filter}&search=${search}`)
    .then(res=>res.text())
    .then(html=>{
        document.querySelector("#usersTable tbody").innerHTML = html;
    });
}

document.getElementById('filterSelect').addEventListener('change', applyFilter);
document.getElementById('searchBox').addEventListener('keyup', applyFilter);
</script>
</body>
</html>
