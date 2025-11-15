<?php
session_start(); // Start the session

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; 
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

try {
    // SQL to fetch all approved posts along with the username
    $sql = "
    SELECT posts.*, users.username 
    FROM posts 
    LEFT JOIN users ON posts.user_id = users.user_id 
    WHERE posts.approved = 1
    ORDER BY posts.id DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "<p style='color:red;'>Query failed: " . $e->getMessage() . "</p>";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_name'])) {
    $product_name = $_POST['product_name'];
    $details = $_POST['details'];
    $category = $_POST['category'];
    $seller_info = $_POST['seller_info'];
    $affiliate_link = isset($_POST['link_url']) ? $_POST['link_url'] : '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $filename = preg_replace("/[^a-zA-Z0-9.\-_]/", "_", basename($_FILES["file"]["name"]));
        $targetFile = $targetDir . $filename;
        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
        $image = $filename;
    } else {
        $image = '';
    }

    // Insert new post into the database (with approval set to 0)
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, seller_info, affiliate_link, category, image, approved, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("issssss", $user_id, $product_name, $details, $seller_info, $affiliate_link, $category, $image);

    if ($stmt->execute()) {
        header("Location: main.php?submitted=1");
        exit();
    } else {
        echo "<script>alert('Error submitting post. Please try again.');</script>";
    }
}

// Handle post deletion by the user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    if ($stmt->execute()) {
        header("Location: main.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting post.";
    }
}

$search = $_GET['search'] ?? ''; 

$searchSql = "
    SELECT posts.*, users.username 
    FROM posts 
    LEFT JOIN users ON posts.user_id = users.user_id 
    WHERE posts.approved = 1
";

if ($search) {
    $searchSql .= " AND (posts.title LIKE ? OR posts.content LIKE ? OR posts.category LIKE ?)";
}

$searchSql .= " ORDER BY posts.id DESC";

$stmt = $conn->prepare($search ? $searchSql : $sql);

if ($search) {
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
}

$stmt->execute();
$result = $stmt->get_result(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">
        <a href="main.php">
            <img src="logo.png" alt="ClickMate Logo" class="logo-img">
        </a>
    </div>

    <ul class="nav-links">
        <li><a href="skin.php">Skin</a></li>
        <li><a href="hair.php">Hair</a></li>
        <li><a href="sets.php">Sets</a></li>
        <li><a href="analytics.php">Analytics</a></li>
    </ul>

    <div class="profile">
        <i class="fas fa-user-circle"></i>
        <span><?php echo htmlspecialchars($username); ?></span>
        <a href="browse.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="main-content">
    <div class="top-bar">
        <form class="search-form" method="get" action="main.php">
            <input type="text" name="search" placeholder="Search ClickMate...." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        <button class="create-blog-btn" id="openModalBtn">+ Create a Blog</button>
    </div>
</div>

<div class="posts-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post-card">
            <?php if (!empty($row['image'])): ?>
                <div class="post-image-wrapper" style="position: relative;">
                    <img src="<?php echo 'uploads/' . htmlspecialchars($row['image']); ?>"
                         alt="Product Image"
                         class="post-image"
                         data-post-id="<?php echo $row['id']; ?>">

                    <?php if ($row['user_id'] == $user_id): ?>
                        <i class="fas fa-trash delete-post-icon"
                           style="position:absolute; top:10px; right:10px; color:red; cursor:pointer; border-radius: 50%; padding: 5px;"
                           data-post-id="<?php echo $row['id']; ?>">
                        </i>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="post-card-content" data-post-id="<?php echo $row['id']; ?>">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_info']); ?></p>

                <?php if (!empty($row['affiliate_link'])): ?>
                    <p>
                        <a href="redirect.php?id=<?php echo $row['id']; ?>" target="_blank" class="buy-button">
                            Order Here
                        </a>
                    </p>
                <?php endif; ?>

                <p><em>Posted by: <?php echo htmlspecialchars($row['username']); ?></em></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</div>

<div class="posts-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post-card">
            <?php if (!empty($row['image'])): ?>
                <div class="post-image-wrapper" style="position: relative;">
                    <img src="<?php echo 'uploads/' . htmlspecialchars($row['image']); ?>"
                         alt="Product Image"
                         class="post-image"
                         data-post-id="<?php echo $row['id']; ?>">

                    <?php if ($row['user_id'] == $user_id): ?>
                        <i class="fas fa-trash delete-post-icon"
                           style="position:absolute; top:10px; right:10px; color:red; cursor:pointer; border-radius: 50%; padding: 5px;"
                           data-post-id="<?php echo $row['id']; ?>">
                        </i>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="post-card-content" data-post-id="<?php echo $row['id']; ?>">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <p><strong>Seller:</strong> <?php echo htmlspecialchars($row['seller_info']); ?></p>

                <?php if (!empty($row['affiliate_link'])): ?>
                    <p>
                        <a href="redirect.php?id=<?php echo $row['id']; ?>" target="_blank" class="buy-button">
                            Order Here
                        </a>
                    </p>
                <?php endif; ?>

                <p><em>Posted by: <?php echo htmlspecialchars($row['username']); ?></em></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</div>

<div id="deleteConfirmModal" class="modal" style="display:none;">
    <div class="modal-content">
        <center><p>Are you sure you want to delete this post?</p><br>
        <div class="modal-buttons">
            <button class="cancel-btn" id="cancelDeleteBtn">Cancel</button>
            <form id="deleteForm" method="post" action="main.php" style="display:inline;">
                <input type="hidden" name="post_id" id="deletePostId">
                <button type="submit" class="done-btn">Delete</button></center>
            </form>
        </div>
    </div>
</div>

<div id="commentLikeModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="closeCommentLikeModal">&times;</span>
        <div id="commentLikeContent">
            <div id="modalImageContainer"></div>
            <button id="likeButton">♡ (<span id="likeCount">0</span>)</button>
            <div id="modalComments"></div>
            <textarea id="commentInput" placeholder="Write a comment..."></textarea>
            <button id="submitComment">Submit</button>
        </div>
    </div>
</div>

<div id="blogModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <h2>Add Information</h2>
        <form class="blog-form" method="post" action="main.php" enctype="multipart/form-data">
            <label>PRODUCT NAME</label><br><br>
            <input type="text" name="product_name" required><br><br> 

            <label>DETAILS</label><br><br>
            <input type="text" name="details" required><br><br>

            <label>SELLER INFORMATION</label><br><br>
            <input type="text" name="seller_info" required><br><br>

            <input type="file" name="file"><br><br>

            <select name="category" required>
                <option value="">-- Select a category --</option>
                <option value="Skin">Skin</option>
                <option value="Hair">Hair</option>
                <option value="Sets">Sets</option>
            </select><br><br>

            <button type="button" class="add-link-btn" id="openLinkModalBtn">+ Add Link</button><br><br>

            <input type="hidden" name="link_url" id="hiddenAffiliateLink">

            <div id="linkModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" id="closeLinkModalBtn">&times;</span>
                    <h2>Add Link</h2>
                    <div class="link-form">
                        <label>URL</label>
                        <input type="url" id="linkInput"><br><br>
                        <div class="form-buttons">
                            <button type="button" class="cancel-btn" id="cancelLinkModalBtn">Cancel</button>
                            <button type="button" class="done-btn" id="saveLinkBtn">Save Link</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-buttons">
                <button type="button" class="cancel-btn" id="cancelModalBtn">Cancel</button>
                <button type="submit" class="done-btn">Done</button>
            </div>
        </form>
    </div>
</div>

<div id="successModal" class="modal" style="display:none;">
    <div class="modal-content">
        <center><p style="font-size: 18px;">Post submitted for admin approval!</p>
        <div class="modal-buttons" style="margin-top: 20px;">
            <button onclick="closeSuccessModal()" class="done-btn">OK</button></center>
        </div>
    </div>
</div>

    <script src="main.js"></script>
</body>
</html>
