<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ClickMate - Browse</title>
    <link rel="stylesheet" href="browse.css">
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <h2 class="logo">
            <img src="logo.png" alt="ClickMate Logo" class="logo-img">
        </h2>
        <nav>
            <a href="login.php" class="btn">Log In</a>
            <a href="signup.php" class="btn signup">Sign Up</a>
        </nav>
    </header>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="text">
            <h1>Discover Products. Read Reviews. Earn from Recommendations.</h1>
            <p>ClickMate is your go-to platform for exploring top-rated affiliate blogs, honest reviews, and curated product picks. Whether you're a reader or a future affiliate, there's something here for everyone.</p>
            <a href="login.php" class="cta-btn">Get Started!</a>
        </div>
        <div class="image">
            <img src="uploads/bgr.png" alt="Preview">
        </div>
    </section>

    <!-- AD SLIDER -->
    <section class="ads">
        <div class="slider-container">
            <div class="slider-controls">
                <button onclick="prevVideo()">&#10094;</button>
                <button onclick="nextVideo()">&#10095;</button>
            </div>
            <div class="slider" id="slider">
                <video class="active" autoplay muted loop>
                    <source src="uploads/ads.mp4" type="video/mp4">
                </video>
                <video autoplay muted loop>
                    <source src="uploads/cetaphil.mp4" type="video/mp4">
                </video>
                <video autoplay muted loop>
                    <source src="uploads/ponds.mp4" type="video/mp4">
                </video>
                <video autoplay muted loop>
                    <source src="uploads/loreal.mp4" type="video/mp4">
                </video>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2025 ClickMate. All rights reserved.</p>
    </footer>

    <script src="browse.js"></script>
</body>
</html>
