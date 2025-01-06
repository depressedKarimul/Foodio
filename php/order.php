<?php
// Start the session
session_start();

// Include the database connection
include 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$itemId = $_POST['item_id'];
$quantity = $_POST['quantity'];

// Fetch item details
$sql = "SELECT * FROM Menu_Items WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$itemResult = $stmt->get_result();

if ($itemResult->num_rows > 0) {
    $item = $itemResult->fetch_assoc();
    $totalAmount = $item['price'] * $quantity;
} else {
    echo "Item not found.";
    exit;
}

// Insert order into Orders table
$sql = "INSERT INTO Orders (user_id, item_id, quantity, total_amount, status) VALUES (?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiid", $userId, $itemId, $quantity, $totalAmount);
$stmt->execute();

// Get the last inserted order ID
$orderId = $stmt->insert_id;

// Redirect to payment page
header("Location: payment.php?order_id=" . $orderId);
exit;
?>
