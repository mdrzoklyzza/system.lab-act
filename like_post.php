<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Check if already liked
$stmt = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$alreadyLiked = $stmt->get_result()->num_rows > 0;

if ($alreadyLiked) {
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
} else {
    $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
}
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();

// Get updated like count
$stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    'like_count' => $row['li
