<?php
session_start();
include 'db.php';

// Debugging mode: show all errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'db.php';
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

try {
    // Check if categories table has 'category_id = 1' for skin
    $sql = "
    SELECT posts.*, users.username 
    FROM posts 
    LEFT JOIN users ON posts.user_id = users.user_id 
    WHERE posts.category = 'Sets' AND posts.approved = 1
    ORDER BY posts.id DESC
";


    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "<p style='color:red;'>Query failed: " . $e->getMessage() . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sets Products - ClickMate</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .post {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .post h3 {
            margin: 0 0 10px;
        }
        .post p {
            white-space: pre-wrap;
        }
        .post a {
            color: #1e90ff;
            text-decoration: underline;
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
        <span><?php echo htmlspecialchars($username); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    </nav>
<center><h2>Sets Product Posts</h2></center>

<div class="posts-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="post-card">
        <?php if (!empty($row['image'])): ?>
            <img src="<?php echo 'uploads/' . htmlspecialchars($row['image']); ?>" alt="Product Image" class="post-image">
        <?php endif; ?>

        <div class="post-card-content">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_info']); ?></p>

            <?php if (!empty($row['affiliate_link'])): ?>
                <p><a href="<?php echo htmlspecialchars($row['affiliate_link']); ?>" target="_blank">Buy Now</a></p>
            <?php endif; ?>

            <p><em>Posted by: <?php echo htmlspecialchars($row['username']); ?></em></p>
        </div>
    </div>
                <?php endwhile; ?>
            </div>
        </div>
</body>
</html>
