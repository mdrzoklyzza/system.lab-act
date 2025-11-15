<?php
include 'db.php';

$post_id = $_GET['post_id'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

echo $result['like_count'];
?>
