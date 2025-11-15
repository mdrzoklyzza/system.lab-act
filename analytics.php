<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

$sql = "
    SELECT id, title, click_count, affiliate_link
    FROM posts
    WHERE user_id = ? AND approved = 1
    ORDER BY click_count DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    th {
    background-color: #f44336;
    color: white;
    font-family: Arial, sans-serif;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
    }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">
        <a href="main.php">
            <img src="logo.png" alt="ClickMate Logo" class="logo-img">
        </a>
    </div>

    <ul class="nav-links">
        <li><a href="skin.php">Skin</a></li>
        <li><a href="hair.php">Hair</a></li>
        <li><a href="sets.php">Sets</a></li>
        <li><a href="analytics.php">Analytics</a></li>
    </ul>

    <div class="profile">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="browse.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="main-content">
    <h2>Your Post Performance</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th><center>Post Title</center></th>
                <th><center>Clicks</center></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo (int)$row['click_count']; ?></td>
                   
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
