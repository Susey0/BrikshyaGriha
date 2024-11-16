<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    // Fetch cart items to calculate total amount
    $cartResult = $conn->query("SELECT p.id AS product_id, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $userId");
    $totalAmount = 0;

    // Prepare for order item storage
    $orderItems = [];

    while ($item = $cartResult->fetch_assoc()) {
        $totalAmount += $item['price'] * $item['quantity'];
        $orderItems[] = [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ];
    }

    // Insert order into the database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, address, phone, total_amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $userId, $name, $address, $phone, $totalAmount);

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id; // Get the ID of the newly created order
        $cartResult = $conn->query("SELECT * FROM cart WHERE user_id = $userId");
        while ($item = $cartResult->fetch_assoc()) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            // Insert into order_items
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmtItem->bind_param("iiid", $orderId, $productId, $quantity, $price);
            $price = $item['price']; // assuming price is fetched from cart

            if (!$stmtItem->execute()) {
                echo "<script>alert('Error inserting order item: " . $stmtItem->error . "');</script>";
            }
            $stmtItem->close();
        }

        // Clear the cart after successful order
        $conn->query("DELETE FROM cart WHERE user_id = $userId");

        echo "<script>alert('Order placed successfully!'); window.location.href = 'order_confirmation.php?order_id=" . $orderId . "';</script>";
    } else {
        echo "<script>alert('Error placing order: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
$conn->close();
?>