<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id'], $_POST['comment_text'])) {
    $post_id = intval($_POST['post_id']);
    $comment_text = trim($_POST['comment_text']);
    $user_id = $_SESSION['user_id'];

    if ($comment_text !== '') {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
        $stmt->execute();
        $stmt->close();
        echo "Comment added";
    } else {
        echo "Empty comment";
    }
}
?>
