<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodio</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/navProfile.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Food<span>io</span></div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Menu</a></li>
            <li><a href="#">Service</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Gallery</a></li>
        </ul>
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn-avatar" id="avatarBtn">
                <div class="avatar">
                    <!-- Static profile image -->
                    <img alt="Avatar" src="https://cdn-icons-png.flaticon.com/512/3781/3781986.png" id="avatarImg" />
                </div>
            </div>
            <ul class="menu dropdown-content" id="dropdownMenu">
                <li>
                    <a href="#">Profile</a>
                </li>
                <li><a href="#">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <script src="main.js"></script>
</body>
</html>
