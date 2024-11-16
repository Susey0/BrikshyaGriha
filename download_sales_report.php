<?php
// Include your database connection file
include 'config.php'; // Adjust the path to your config file

// Check if user_id is provided
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $userId = intval($_GET['user_id']); // Sanitize user input

    // Prepare the SQL query to fetch sales data for the specified user
    $query = "
        SELECT s.id AS sale_id, s.order_id, o.total_amount AS order_total, 
               p.name AS product_name, s.quantity, s.price, s.total_amount AS sale_total, 
               o.full_name, o.address, o.phone, 
               s.created_at AS sale_date
        FROM sales s
        JOIN orders o ON s.order_id = o.id
        JOIN products p ON s.product_id = p.id
        WHERE o.user_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    // Fetch and output the data
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report.csv"');
    $output = fopen('php://output', 'w');

    // Write the header row
    fputcsv($output, [
        'Sale ID',
        'Order ID',
        'Customer Name',
        'Customer Address',
        'Customer Phone',
        'Product Name',
        'Quantity',
        'Price',
        'Total Amount',
        'Sale Date'
    ]);

    // Fetch and output the data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['sale_id'],
            $row['order_id'],
            $row['full_name'],
            $row['address'],
            $row['phone'],
            $row['product_name'],
            $row['quantity'],
            number_format((float) $row['price'], 2, '.', ''), // Format price with 2 decimal places
            number_format((float) $row['sale_total'], 2, '.', ''), // Format total amount with 2 decimal places
            date('Y-m-d', strtotime($row['sale_date'])) // Ensure the date format is YYYY-MM-DD
        ]);
    }

    fclose($output);
    exit();

} else {
    echo "User ID not specified.";
}

// Close the database connection
$conn->close();
?>