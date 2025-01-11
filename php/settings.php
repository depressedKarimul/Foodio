<?php
session_start();
include 'database.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = "";

// Fetch the current user details
$sql = "SELECT name, email, phone_number, address FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Update user details
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    if ($password) {
        $sql = "UPDATE Users SET name = ?, email = ?, password = ?, phone_number = ?, address = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $password, $phone_number, $address, $userId);
    } else {
        $sql = "UPDATE Users SET name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone_number, $address, $userId);
    }

    if ($stmt->execute()) {
        $message = "Profile updated successfully.";
    } else {
        $message = "Error updating profile.";
    }
}

// Delete profile
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete'])) {
    $sql = "DELETE FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        $message = "Error deleting profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #a71d2a;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Settings</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="settings.php">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            
            <label for="password">Password (Leave blank to keep current)</label>
            <input type="password" name="password" id="password">
            
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            
            <label for="address">Address</label>
            <textarea name="address" id="address" rows="4" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            
            <button type="submit" name="update">Update Profile</button>
            <button type="submit" name="delete" class="delete-btn">Delete Profile</button>
        </form>
    </div>
</body>
</html>
