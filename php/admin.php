
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
    // Initialize variables
    $name = $description = $price = '';
    $availability = 0;

    // Check if form fields are set before accessing them
    if (isset($_POST['name'])) {
        $name = trim($_POST['name']);
    }
    if (isset($_POST['description'])) {
        $description = trim($_POST['description']);
    }
    if (isset($_POST['price'])) {
        $price = trim($_POST['price']);
    }
    $availability = isset($_POST['availability']) ? 1 : 0; // Checkbox for availability

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

<?php
include 'database.php'; // Include the database connection

$message = "";

// Handle item deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    $itemId = $_POST['item_id'];

    $sql = "DELETE FROM Menu_Items WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);

    if ($stmt->execute()) {
        $message = "Item deleted successfully.";
    } else {
        $message = "Error deleting item.";
    }
}

// Fetch all menu items
$sql = "SELECT * FROM Menu_Items";
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
    <link rel="stylesheet" href="styles/AdminMenuItems.css">
    <link rel="stylesheet" href="styles/AdminAddMenuItem.css">

    <style>
        
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
                    <a href="ACProfile.php">Profile</a>
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





    <!-- Fech item -->
    <div class="container">
        <h2>Menu Items</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['item_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['availability'] ? 'Available' : 'Not Available'; ?></td>
                            <td>
                                <form method="POST" action="admin.php" style="display:inline;">
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <button type="submit" name="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


<!-- Customer List -->
    <?php
// Include the database connection
include 'database.php';

// Query to fetch all customers
$sql = "SELECT user_id, name, email, phone_number, address, created_at FROM Users WHERE user_type = 'customer'";
$result = $conn->query($sql);

?>

<!-- HTML to display customer list -->
<div class="container">
    <h2>Customer List</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No customers found.</p>
    <?php endif; ?>
</div>



<?php
// Include the database connection
include 'database.php';

// Query to fetch customers, their purchased products, quantities, and total amount paid
$sql = "
    SELECT 
        Users.user_id,
        Users.name AS customer_name,
        Users.email,
        Menu_Items.name AS product_name,
        Orders.quantity,
        Orders.total_amount,
        Orders.order_date
    FROM 
        Orders
    INNER JOIN Users ON Orders.user_id = Users.user_id
    INNER JOIN Menu_Items ON Orders.item_id = Menu_Items.item_id
    ORDER BY Orders.order_date DESC
";

$result = $conn->query($sql);
?>

<!-- HTML to display the list -->
<div class="container">
    <h2>Customer Purchases</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Paid ($)</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No purchases found.</p>
    <?php endif; ?>
</div>


<?php
// Query to fetch all delivery men
$sql = "SELECT user_id, name, email, phone_number, address, created_at FROM Users WHERE user_type = 'delivery_man'";
$result = $conn->query($sql);
?>

<div class="container">
    <h2>Delivery Men List</h2>

    <!-- Display success/error message -->
    <?php if (!empty($message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No delivery men found.</p>
    <?php endif; ?>
</div>

<?php
// Close the database connection
$conn->close();
?>


<?php


// Include the database connection
include 'database.php';

// Fetch the delivery details
$sql = "
    SELECT u1.name AS delivery_man_name, 
           u2.name AS customer_name, 
           m.name AS product_name, 
           o.quantity, 
           o.total_amount, 
           d.delivery_time
    FROM Delivery d
    JOIN Users u1 ON d.user_id = u1.user_id  -- Join to get delivery man's name
    JOIN Orders o ON d.order_id = o.order_id  -- Join to get order details
    JOIN Users u2 ON o.user_id = u2.user_id  -- Join to get customer's name
    JOIN Menu_Items m ON o.item_id = m.item_id  -- Join to get product details
    WHERE u1.user_type = 'delivery_man'
    ORDER BY d.delivery_time DESC
";

$result = $conn->query($sql);
?>

<!-- HTML to display the data -->
<div class="container">
    <h2>Delivery Man's Deliveries</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Delivery Man</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Delivery Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['delivery_man_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_time']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No delivery information found.</p>
    <?php endif; ?>
</div>


    
    <script src="main.js"></script>
</body>

</html>