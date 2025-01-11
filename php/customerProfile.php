<?php
// Start session and include database connection
session_start();
include 'database.php';

// Check if the user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch customer information
$sqlUser = "SELECT * FROM Users WHERE user_id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$customer = $userResult->fetch_assoc();

// Fetch all delivered orders for the logged-in customer
$sqlOrders = "SELECT o.order_id, o.quantity, o.total_amount, o.delivery_date, m.name AS item_name 
              FROM Orders o
              JOIN Menu_Items m ON o.item_id = m.item_id
              WHERE o.user_id = ? AND o.status = 'delivered'";
$stmtOrders = $conn->prepare($sqlOrders);
$stmtOrders->bind_param("i", $userId);
$stmtOrders->execute();
$orderResults = $stmtOrders->get_result();

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Insert rating and review into Ratings_And_Reviews table
    $sqlReview = "INSERT INTO Ratings_And_Reviews (order_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
    $stmtReview = $conn->prepare($sqlReview);
    $stmtReview->bind_param("iiis", $orderId, $userId, $rating, $review);
    $stmtReview->execute();

    $message = "Your review has been submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
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
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .profile, .order, .message {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }
        .profile h2 {
            text-align: center;
        }
        .order h3 {
            margin: 0;
            color: #007bff;
        }
        .order p, .profile p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .message {
            text-align: center;
            padding: 10px;
            color: #fff;
            background: #28a745;
        }
        .order form {
            margin-top: 10px;
        }
        .stars {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .stars input {
            display: none;
        }
        .stars label {
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
        }
        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: #ffcc00;
        }
        .order textarea, .order button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .order button {
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .order button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Customer Profile</h1>

    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="profile">
        <img src="https://cdn-icons-png.flaticon.com/512/3781/3781986.png" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($customer['name']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($customer['phone_number']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></p>
        <p><strong>Member Since:</strong> <?php echo $customer['created_at']; ?></p>
    </div>

    <h2>Delivered Orders</h2>
    <?php if ($orderResults->num_rows > 0): ?>
        <?php while ($order = $orderResults->fetch_assoc()): ?>
            <div class="order">
                <h3>Order ID: <?php echo $order['order_id']; ?></h3>
                <p><strong>Item:</strong> <?php echo htmlspecialchars($order['item_name']); ?></p>
                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Delivery Date:</strong> <?php echo $order['delivery_date']; ?></p>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <div class="stars">
                        <input type="radio" name="rating" id="star5-<?php echo $order['order_id']; ?>" value="5">
                        <label for="star5-<?php echo $order['order_id']; ?>">&#9733;</label>
                        <input type="radio" name="rating" id="star4-<?php echo $order['order_id']; ?>" value="4">
                        <label for="star4-<?php echo $order['order_id']; ?>">&#9733;</label>
                        <input type="radio" name="rating" id="star3-<?php echo $order['order_id']; ?>" value="3">
                        <label for="star3-<?php echo $order['order_id']; ?>">&#9733;</label>
                        <input type="radio" name="rating" id="star2-<?php echo $order['order_id']; ?>" value="2">
                        <label for="star2-<?php echo $order['order_id']; ?>">&#9733;</label>
                        <input type="radio" name="rating" id="star1-<?php echo $order['order_id']; ?>" value="1">
                        <label for="star1-<?php echo $order['order_id']; ?>">&#9733;</label>
                    </div>
                    <label for="review">Review:</label>
                    <textarea name="review" rows="4" required></textarea>
                    <button type="submit">Submit Review</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No delivered orders found.</p>
    <?php endif; ?>
</div>
</body>
</html>
