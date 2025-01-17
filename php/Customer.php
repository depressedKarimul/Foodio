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
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all menu items
$sql = "SELECT * FROM Menu_Items WHERE availability = 1";  // Only available items
$result = $conn->query($sql);
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
.menu-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 20px;
    background-color: #f4f4f4;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.menu-container h2 {
    text-align: center;
    color: #333;
    font-size: 2rem;
    margin-bottom: 30px;
    font-weight: bold;
}

/* Menu Items Container */
.menu-items {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

/* Food Card Styling */
.food-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    text-align: center;
}

.food-card:hover {
    transform: translateY(-10px);
}

.food-card h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 15px;
    font-weight: bold;
}

.food-card p {
    color: #666;
    font-size: 1rem;
    margin-bottom: 15px;
}

.food-card .price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #2c8f7e;
    margin-bottom: 20px;
}

/* Form Styles */
.food-card form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.food-card input[type="number"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 15px;
    width: 60%;
    font-size: 1rem;
    text-align: center;
}

.food-card button {
    padding: 10px 20px;
    background-color: #2c8f7e;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;
}

.food-card button:hover {
    background-color: #217a64;
}

.food-card input[type="number"]:focus,
.food-card button:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(44, 143, 126, 0.6);
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
                    <a href="customerProfile.php">Profile</a>
                </li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>



    <div class="menu-container">
        <h2>Our Menu</h2>
        <div class="menu-items">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='food-card'>";
                    
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<p>Price: $" . number_format($row['price'], 2) . "</p>";
                    echo "<form action='order.php' method='POST'>";
                    echo "<input type='hidden' name='item_id' value='" . $row['item_id'] . "'>";
                    echo "<input type='number' name='quantity' min='1' required placeholder='Quantity'>";
                    echo "<button type='submit'>Order</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "No items available.";
            }
            ?>
        </div>
    </div>

    <?php
    include 'Footer.html';
    ?>

    <script src="main.js"></script>
</body>
</html>
