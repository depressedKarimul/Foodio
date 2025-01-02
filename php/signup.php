<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "foodie";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error array
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $phone_number = $conn->real_escape_string(trim($_POST['phone_number']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $user_type = $conn->real_escape_string(trim($_POST['user_type']));

    // Handle file upload
    $profilePicture = null;
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $uploadDir = "C:/xampp/htdocs/Foodio/upload/"; // Ensure this folder exists
        $fileName = time() . "_" . basename($_FILES["profile_picture"]["name"]); // Unique filename
        $targetPath = $uploadDir . $fileName;

        // Check file type
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetPath)) {
                $profilePicture = "upload/" . $fileName; // Save relative path
            } else {
                $errors[] = "Failed to upload profile picture.";
            }
        } else {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Insert data into the database if no errors
    if (empty($errors)) {
        $sql = "INSERT INTO Users (name, email, password, phone_number, address, user_type, profile_picture)
                VALUES ('$name', '$email', '$password', '$phone_number', '$address', '$user_type', '$profilePicture')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to login.php after successful signup
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodio - Sign Up</title>
   <link rel="stylesheet" href="logreg.css">
</head>
<body>
    <section>
        <section> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> <span></span> 

       
        <div class="signin">
            <div class="content">
                <h2>Sign Up</h2>
                <form class="form" action="/Foodio/php/signup.php" method="POST" enctype="multipart/form-data">
                    <div class="inputBox">
                        <input type="text" name="name" required>
                        <i>Full Name</i>
                    </div>
                    <div class="inputBox">
                        <input type="email" name="email" required>
                        <i>Email</i>
                    </div>
                    <div class="inputBox">
                        <input type="password" name="password" required>
                        <i>Password</i>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="phone_number" required>
                        <i>Phone Number</i>
                    </div>
                    <div class="inputBox">
                        <textarea name="address" rows="3" required></textarea>
                        <i>Address</i>
                    </div>
                    <div class="inputBox">
                        <select name="user_type" required>
                            <option value="" disabled selected>Select User Type</option>
                            <option value="admin">Admin</option>
                            <option value="delivery_man">Delivery Man</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="inputBox">
                        <input type="file" name="profile_picture" accept="image/*">
                        <i><small>Profile Picture</small></i>
                    </div>
                    <div class="inputBox">
                        <input type="submit" value="Sign Up">
                    </div>
                </form>
                <?php if (!empty($errors)): ?>
                    <div class="error-messages">
                        <h3>Errors:</h3>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
  
</body>
</html>
