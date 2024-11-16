<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login-form.php');
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// If the form is submitted to update the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if any cart items are updated
    if (isset($_POST['final_quantities'])) {
        // Prepare to update the quantities
        $updatedQuantities = $_POST['final_quantities'];

        // Iterate over the updated quantities
        foreach ($updatedQuantities as $cart_id => $quantity) {
            $final_quantity = (int) $quantity;

            // Fetch product details to ensure enough stock is available
            $query = "SELECT p.stock FROM cart c 
                      JOIN products p ON c.product_id = p.id 
                      WHERE c.id = ? AND c.user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if ($product) {
                $available_stock = $product['stock'];

                // Check if the quantity is valid
                if ($final_quantity > $available_stock) {
                    $_SESSION['error_message'] = "You cannot add more than $available_stock items for this product.";
                    header("Location: cart.php");
                    exit;
                } else if ($final_quantity < 1) {
                    $_SESSION['error_message'] = "Quantity cannot be less than 1.";
                    header("Location: cart.php");
                    exit;
                }

                // Update cart with new quantity
                $update_cart_query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($update_cart_query);
                $stmt->bind_param("iii", $final_quantity, $cart_id, $user_id);
                $stmt->execute();

                // Update product stock based on the new quantity
                $query_stock = "SELECT stock FROM products WHERE id = ?";
                $stmt = $conn->prepare($query_stock);
                $stmt->bind_param("i", $product['id']);
                $stmt->execute();
                $result_stock = $stmt->get_result()->fetch_assoc();
                $current_stock = $result_stock['stock'];

                $new_stock = $current_stock - $final_quantity;
                $update_stock_query = "UPDATE products SET stock = ? WHERE id = ?";
                $stmt = $conn->prepare($update_stock_query);
                $stmt->bind_param("ii", $new_stock, $product['id']);
                $stmt->execute();
            } else {
                $_SESSION['error_message'] = "Cart item not found.";
                header("Location: cart.php");
                exit;
            }
        }

        $_SESSION['success_message'] = "Cart updated successfully!";
    }
}

// Redirect back to the cart page
header("Location: cart.php");
exit;
?>
