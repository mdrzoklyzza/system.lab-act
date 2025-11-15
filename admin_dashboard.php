<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE posts SET approved = 1 WHERE id = ?");
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($action === 'edit') {
        header("Location: edit_post.php?id=" . $id);
        exit();
    }
}

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalPosts = $conn->query("SELECT COUNT(*) AS total FROM posts")->fetch_assoc()['total'];
$totalCategories = $conn->query("SELECT COUNT(DISTINCT category) AS total FROM posts")->fetch_assoc()['total'];

$sql = "
SELECT posts.id, posts.title, posts.content, posts.affiliate_link, posts.seller_info, users.username, posts.category
FROM posts
LEFT JOIN users ON posts.user_id = users.user_id
LEFT JOIN categories ON posts.category = categories.category_id
WHERE posts.approved = 0
";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - Pending Blogs</title>
  <link rel="stylesheet" href="admin_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <nav class="navbar">
    <div class="logo">
      <img src="logo.png" alt="ClickMate Logo" class="logo-img">
    </div>
    <div class="profile">
      <i class="fas fa-user-circle"></i>
      <span><?php echo htmlspecialchars($admin_username); ?></span>
      <a href="browse.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <div class="dashboard-cards">
    <div class="card">
      <i class="fas fa-users card-icon"></i>
      <h3>Total Users</h3>
      <p><?php echo $totalUsers; ?></p>
    </div>
    <div class="card">
      <i class="fas fa-box-open card-icon"></i>
      <h3>Total Products</h3>
      <p><?php echo $totalPosts; ?></p>
    </div>
    <div class="card">
      <i class="fas fa-layer-group card-icon"></i>
      <h3>Total Categories</h3>
      <p><?php echo $totalCategories; ?></p>
    </div>
  </div>

  <center><h2>Pending Blog Posts for Approval</h2>

  <table>
    <tr>
      <th><center>Product Name</center></th>
      <th><center>Details</center></th>
      <th><center>Seller Info</center></th>
      <th><center>Link</center></th>
      <th><center>Posted By</center></th>
      <th><center>Category</center></th>
      <th><center>Actions</center></th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo nl2br(htmlspecialchars($row['content'])); ?></td>
      <td><?php echo htmlspecialchars($row['seller_info']); ?></td>
      <td><?php echo htmlspecialchars($row['affiliate_link']); ?></td>
      <td><?php echo htmlspecialchars($row['username']); ?></td>
      <td><?php echo htmlspecialchars($row['category']); ?></td>
      <td>
        <a href="#" class="action-btn approve" onclick="openModal('approve', <?php echo $row['id']; ?>)">Approve</a>
        <a href="admin_dashboard.php?action=edit&id=<?php echo $row['id']; ?>" class="action-btn edit">Edit</a>
        <a href="#" class="action-btn delete" onclick="openModal('delete', <?php echo $row['id']; ?>)">Delete</a>
      </td>
    </tr>
    <?php } ?>
  </table></center>


  <div id="confirmationModal" class="modal">
    <div class="modal-content">
      <p id="modalMessage">Are you sure?</p>
      <div class="modal-buttons">
        <button onclick="proceedAction()" class="yes-btn">Yes</button>
        <button onclick="closeModal()" class="no-btn">No</button>
      </div>
    </div>
  </div>

  <script>
    let selectedAction = '';
    let selectedId = '';

    function openModal(action, id) {
      selectedAction = action;
      selectedId = id;
      document.getElementById('modalMessage').textContent =
        action === 'approve' ? 'Approve this post?' : 'Delete this post?';
      document.getElementById('confirmationModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('confirmationModal').style.display = 'none';
      selectedAction = '';
      selectedId = '';
    }

    function proceedAction() {
      if (selectedAction && selectedId) {
        window.location.href = `admin_dashboard.php?action=${selectedAction}&id=${selectedId}`;
      }
    }
  </script>
</body>
</html>
