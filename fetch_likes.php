<?php
session_start();
include 'db.php';

$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];

// Get total like count
$stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$like_count = $row['like_count'];

// Check if this user liked the post
$stmt = $conn->prepare("SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$user_liked = $stmt->get_result()->num_rows > 0;

echo json_encode([
    'like_count' => $like_count,
    'liked_by_user' => $user_liked
]);
?>
