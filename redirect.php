<?php
include 'db.php';

if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);

    // Increase click count
    $update = $conn->prepare("UPDATE posts SET click_count = click_count + 1 WHERE id = ?");
    $update->bind_param("i", $post_id);
    $update->execute();

    // Get the affiliate link
    $stmt = $conn->prepare("SELECT affiliate_link FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $link = $row['affiliate_link'];
        header("Location: " . $link); // Redirect to actual affiliate link
        exit();
    } else {
        echo "Affiliate link not found.";
    }
} else {
    echo "Invalid post ID.";
}
?>
