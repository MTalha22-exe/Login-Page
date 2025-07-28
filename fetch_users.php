<?php
session_start();
if ($_SESSION['role'] !== 'admin') exit;

$conn = new mysqli("localhost", "root", "", "login_db");

$filter = $_GET['filter'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit=5;
$offset=($page-1)*$limit;

$where="WHERE 1";
$params=[]; $types="";

if($filter!=""){ $where.=" AND role=?"; $params[]=$filter; $types.="s"; }
if($search!=""){ $where.=" AND username LIKE ?"; $params[]="%$search%"; $types.="s"; }

$sql="SELECT id, username, role FROM users $where LIMIT ? OFFSET ?";
$stmt=$conn->prepare($sql);
if(!empty($params)){
    $types.="ii"; $params[]= $limit; $params[]=$offset;
    $stmt->bind_param($types,...$params);
}else{
    $stmt->bind_param("ii",$limit,$offset);
}
$stmt->execute(); $res=$stmt->get_result();
while($u=$res->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($u['id']) ?></td>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['role']) ?></td>
</tr>
<?php endwhile; ?>
