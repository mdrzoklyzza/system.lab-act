<?php
include 'db.php';

if (isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
    $stmt = $conn->prepare("
        SELECT c.comment_text, u.username, c.created_at 
        FROM comments c 
        JOIN users u ON c.user_id = u.user_id 
        WHERE c.post_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $formattedTime = date("M j, Y · h:i A", strtotime($row['created_at']));
        echo "<div class='comment-item' style='margin-bottom:10px;'>
                <p style='margin:0;'>
                    <i class='fas fa-user-circle'></i> 
                    <strong>" . htmlspecialchars($row['username']) . "</strong>
                </p>
                <p style='margin:5px 0;'>" . htmlspecialchars($row['comment_text']) . "</p>
                <small style='color:gray;'>" . $formattedTime . "</small>
              </div>";
    }
}
?>
