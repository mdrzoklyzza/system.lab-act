<?php
include 'db.php';

$post_id = $_GET['post_id'] ?? 0;

$stmt = $conn->prepare("SELECT comments.comment_text, users.username, comments.created_at
                        FROM comments
                        JOIN users ON comments.user_id = users.user_id
                        WHERE post_id = ?
                        ORDER BY comments.created_at DESC");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div class='comment'>";
    echo "<strong>" . htmlspecialchars($row['username']) . "</strong>: ";
    echo "<span>" . htmlspecialchars($row['comment_text']) . "</span>";
    echo "<small> — " . date("M d, H:i", strtotime($row['created_at'])) . "</small>";
    echo "</div>";
}
?>
