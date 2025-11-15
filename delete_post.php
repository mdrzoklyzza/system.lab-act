<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);

    // Check ownership before deleting
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);

    if ($stmt->execute()) {
        header("Location: main.php?msg=deleted");
    } else {
        echo "Error deleting post.";
    }
}
?>
