<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

<?php


// Include the database connection
include 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Initialize error and success messages
$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $availability = isset($_POST['availability']) ? 1 : 0; // Availability check

    // Validate form data
    if (empty($name) || empty($price)) {
        $error = "Name and Price are required!";
    } else {
        // Insert the menu item into the database
        $sql = "INSERT INTO Menu_Items (name, description, price, availability) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $name, $description, $price, $availability);
        
        if ($stmt->execute()) {
            $success = "Menu item added successfully!";
        } else {
            $error = "Failed to add menu item. Please try again.";
        }
    }
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

    <style>
/* General Styles */
.content {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.8); /* Reduced opacity */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px); /* Adds a blur effect to the background */
}

.content h2 {
    text-align: center;
    color: #333;
}

/* Message Styles */
.error, .success {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
}

.error {
    background-color: rgba(255, 229, 229, 0.8); /* Adjusted opacity */
    color: #d9534f;
}

.success {
    background-color: rgba(229, 255, 229, 0.8); /* Adjusted opacity */
    color: #5cb85c;
}

/* Form Styles */
form .inputBox {
    margin-bottom: 15px;
}

form label {
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-weight: bold;
}

form input[type="text"],
form input[type="number"],
form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
    background-color: rgba(255, 255, 255, 0.8); /* Adjusted opacity */
}

form textarea {
    resize: vertical;
}

form input[type="submit"] {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

form input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Checkbox Styles */
form input[type="checkbox"] {
    margin-right: 10px;
}

input:checked {
    accent-color: #007bff;
}


    </style>
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


    <div class="content">
    <h2>Add Menu Item</h2>

    <!-- Display error or success message -->
    <?php if (!empty($error)): ?>
        <div class="error" style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success" style="color: green;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Menu Item Form -->
    <form method="POST" action="admin.php">
        <div class="inputBox">
            <label for="name">Item Name:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="inputBox">
            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea>
        </div>
        <div class="inputBox">
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" required>
        </div>
        <div class="inputBox">
            <label for="availability">Available:</label>
            <input type="checkbox" name="availability" id="availability" checked>
        </div>
        <div class="inputBox">
            <input type="submit" value="Add Item">
        </div>
    </form>
</div>

    <script src="main.js"></script>
</body>
</html>
