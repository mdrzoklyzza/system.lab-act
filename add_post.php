<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $seller_info = $_POST["seller_info"];
    $affiliate_link = $_POST["affiliate_link"];
    $imagePath = "";

    // Upload image
    if (!empty($_FILES["file"]["name"])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = basename($_FILES["file"]["name"]);
        $targetFile = $targetDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        } else {
            echo "Image upload failed.";
        }
    }

    $sql = "INSERT INTO posts (user_id, title, content, seller_info, affiliate_link, image) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $title, $content, $seller_info, $affiliate_link, $imagePath);

    if ($stmt->execute()) {
        echo "Post submitted for approval.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
