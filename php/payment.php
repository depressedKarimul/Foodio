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

$orderId = $_GET['order_id'];
$userId = $_SESSION['user_id'];

$successMessage = '';

// Fetch order details along with the item name from Menu_Items table
$sql = "SELECT o.*, m.name, m.price 
        FROM Orders o 
        JOIN Menu_Items m ON o.item_id = m.item_id 
        WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$orderResult = $stmt->get_result();

if ($orderResult->num_rows > 0) {
    $order = $orderResult->fetch_assoc();
} else {
    echo "Order not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $paymentMethod = $_POST['payment_method'];

    // Insert payment into Payments table
    $sql = "INSERT INTO Payments (order_id, payment_method, payment_status, payment_date) VALUES (?, ?, 'pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $orderId, $paymentMethod);
    $stmt->execute();

    // Update order status to "accepted"
    $sql = "UPDATE Orders SET status = 'accepted' WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    // Set success message
    $successMessage = "Payment pending. Your order is now accepted!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
    /* General Card Styling */
.card {
  max-width: 400px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
  font-family: Arial, sans-serif;
}

/* Card Title */
.card h2, .card h3 {
  margin: 0 0 15px;
  font-weight: bold;
  color: #333;
  text-align: center;
}

/* Order Details */
.card p {
  margin: 10px 0;
  color: #555;
  font-size: 14px;
}

/* Payment Form */
.card form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.card select {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
  color: #333;
}

.card button {
  padding: 10px;
  border: none;
  border-radius: 5px;
  background-color: #007bff;
  color: #fff;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.card button:hover {
  background-color: #0056b3;
}

/* Success Message */
.success-message {
  text-align: center;
  color: green;
  font-size: 16px;
  margin-top: 20px;
}
    </style>
</head>
<body>
<div class="card">
  <h2>Order Details</h2>
  <p>Item: <?php echo htmlspecialchars($order['name']); ?></p>
  <p>Quantity: <?php echo $order['quantity']; ?></p>
  <p>Total Amount: $<?php echo number_format($order['price'] * $order['quantity'], 2); ?></p>

  <h3>Payment Method</h3>
  <form method="POST" action="payment.php?order_id=<?php echo $orderId; ?>">
    <select name="payment_method">
      <option value="cash">Cash</option>
      <option value="credit_card">Credit Card</option>
      <option value="mobile_payment">Mobile Payment</option>
    </select>
    <button type="submit">Proceed with Payment</button>
  </form>
</div>

<?php if ($successMessage): ?>
  <div class="success-message">
    <?php echo $successMessage; ?>
  </div>
<?php endif; ?>

</body>
</html>
