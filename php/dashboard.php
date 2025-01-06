<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the profile picture from the session
$profilePic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'php/upload/default-avatar.png';
?>
