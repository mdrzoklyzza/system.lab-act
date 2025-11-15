<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    if ($admin_username === 'admin' && $admin_password === '123') {
        $_SESSION['admin_username'] = $admin_username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Incorrect admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Clickmate</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <form method="post">
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Admin Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br><br><br>
        <button type="submit">Login</button><br>
    </form>
</body>
</html>
