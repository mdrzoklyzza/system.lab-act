<?php
session_start();
include 'db.php';


if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$post_id = $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $seller_info = $_POST['seller_info'];
    $affiliate_link = $_POST['affiliate_link'];
    $category = $_POST['category'];

    $sql = "UPDATE posts SET title = ?, content = ?, seller_info = ?, affiliate_link = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error . " | SQL: " . $sql);
    }
    $stmt->bind_param("sssssi", $title, $content, $seller_info, $affiliate_link, $category, $post_id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}

$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error . " | SQL: " . $sql);
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
if (!$post) {
    echo "Post not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog Post</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        form {
            max-width: 600px;
            margin: 30px auto;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
     
        label {
            font-weight: bold;
        }
        .update-btn {
            padding: 10px 20px;
            background-color: #fb4428;
            color: white;
            border: none;
            cursor: pointer;
        }

        .cancel-btn {
            padding: 10px 20px;
            background-color: #fb4428;
            opacity: 0.5;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <center><h2>Edit Blog Post</h2></center>

    <form method="POST">
        <label>Product Name (Title):</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

        <label>Content / Description:</label>
        <textarea name="content" rows="6" required><?php echo htmlspecialchars($post['content']); ?></textarea>

        <label>Seller Info:</label>
        <input type="text" name="seller_info" value="<?php echo htmlspecialchars($post['seller_info']); ?>" required>

        <label>Affiliate Link:</label>
        <input type="text" name="affiliate_link" value="<?php echo htmlspecialchars($post['affiliate_link']); ?>">

        <label>Category ID:</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($post['category']); ?>" required>

        <input type="submit" value="Update Post" class="update-btn">
        <a href="admin_dashboard.php">
            <button type="button" class="cancel-btn">Cancel</button>
        </a>
    </form>
</body>
</html>
