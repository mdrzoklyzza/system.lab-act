<?php
include 'db.php';

$alert = "";
$show_modal = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $alert = "Passwords do not match!";
        $show_modal = true;
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $alert = "Username already taken!";
            $show_modal = true;
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                $alert = "Signup successful! You can now log in.";
                $show_modal = true;
            } else {
                $alert = "Error: " . $stmt->error;
                $show_modal = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clickmate Sign Up</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
<form method="post">
    <h2>Register</h2>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br><br>
    <button type="submit">Sign Up</button><br>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</form>

<div id="messageModal" class="modal">
    <div class="modal-content">
        <p><?php echo htmlspecialchars($alert); ?></p>
        <div class="modal-buttons">
            <button onclick="closeModal()">OK</button>
        </div>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}

<?php if ($show_modal): ?>
    document.getElementById('messageModal').style.display = 'flex';
<?php endif; ?>
</script>
</body>
</html>
