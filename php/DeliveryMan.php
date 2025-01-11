<?php
// Start session and include database connection
session_start();
include 'database.php';

// Check if the user is logged in and is a delivery man
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'delivery_man') {
    header("Location: login.php");
    exit;
}

// Fetch undelivered orders
$sql = "SELECT o.order_id, o.quantity, o.total_amount, o.order_date, o.status, 
               m.name AS item_name, u.name AS customer_name, u.address 
        FROM Orders o
        JOIN Menu_Items m ON o.item_id = m.item_id
        JOIN Users u ON o.user_id = u.user_id
        WHERE o.status != 'delivered'";
$result = $conn->query($sql);

// Handle delivery update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    $deliveryManId = $_SESSION['user_id'];

    // Insert delivery data into Delivery table
    $sqlDelivery = "INSERT INTO Delivery (order_id, user_id, status, delivery_time)
                    VALUES (?, ?, 'delivered', NOW())";
    $stmtDelivery = $conn->prepare($sqlDelivery);
    $stmtDelivery->bind_param("ii", $orderId, $deliveryManId);
    $stmtDelivery->execute();

    // Update the status of the order
    $sqlUpdateOrder = "UPDATE Orders SET status = 'delivered', delivery_date = NOW() WHERE order_id = ?";
    $stmtUpdateOrder = $conn->prepare($sqlUpdateOrder);
    $stmtUpdateOrder->bind_param("i", $orderId);
    $stmtUpdateOrder->execute();

    $message = "Order ID $orderId has been successfully marked as delivered.";
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .order {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .order h2 {
            margin: 0;
            font-size: 18px;
            color: #007bff;
        }
        .order p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .order button {
            padding: 10px 15px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .order button:hover {
            background: #218838;
        }
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            color: #fff;
            background: #28a745;
            border-radius: 5px;
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




    <div class="container">
    <h1>Undelivered Orders</h1>
    
    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="order">
                <h2>Order ID: <?php echo $order['order_id']; ?></h2>
                <p>Item: <?php echo htmlspecialchars($order['item_name']); ?></p>
                <p>Quantity: <?php echo $order['quantity']; ?></p>
                <p>Total Amount: $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p>Order Date: <?php echo $order['order_date']; ?></p>
                <p>Customer: <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p>Address: <?php echo htmlspecialchars($order['address']); ?></p>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <button type="submit">Mark as Delivered</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No undelivered orders found.</p>
    <?php endif; ?>
</div>

    <script src="main.js"></script>
</body>
</html>
