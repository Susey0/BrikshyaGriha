<?php
session_start();
include 'config.php';

if (isset($_GET['id'])) {
    $cartId = intval($_GET['id']);

    // Fetch the product's available stock
    $stmt = $conn->prepare("SELECT product_id, quantity FROM cart WHERE id = ?");
    $stmt->bind_param('i', $cartId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItem = $result->fetch_assoc();

    if ($cartItem) {
        $productId = $cartItem['product_id'];
        $quantity = $cartItem['quantity'];

        // Fetch the current stock of the product
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $availableStock = $product['stock'];

            // Remove the item from the cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->bind_param('i', $cartId);
            $stmt->execute();

            $_SESSION['success_message'] = "Item removed from cart.";
        }
    }
}

header('Location: cart.php'); // Redirect back to the cart page
exit;
?>