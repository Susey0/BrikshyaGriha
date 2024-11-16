<?php
// Include your database connection file
include 'config.php'; // Adjust the path to your config file

// Prepare the SQL query
$query = "
    SELECT s.id AS sale_id, s.order_id, o.user_id, o.total_amount AS order_total, 
           p.name AS product_name, s.quantity, s.price, s.total_amount AS sale_total, 
           o.full_name, o.address, o.phone, 
           s.created_at AS sale_date
    FROM sales s
    JOIN orders o ON s.order_id = o.id
    JOIN products p ON s.product_id = p.id
";

// Execute the query
$salesResult = $conn->query($query);

// Start the HTML structure
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <style>
        body {
            font-family: "Helvetica Neue", Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        header {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            color: white;
            border-radius: 5px;
        }
        h1 {
            margin: 0;
        }
        h3 {
            margin-top: 20px;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            text-decoration: none;
            color: white;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .download-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #2196F3;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-top: 10px;
            text-align: center;
        }
        .download-btn:hover {
            background-color: #1976D2;
        }
        .customer-info {
            background-color: #e7f5e0;
            border-left: 5px solid #4CAF50;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Sales Report</h1>
    </header>';

$currentUserId = null;

while ($row = $salesResult->fetch_assoc()) {
    // Start new customer section if user_id changes
    if ($currentUserId !== $row['user_id']) {
        if ($currentUserId !== null) {
            echo "</table><br/>";
        }

        $currentUserId = $row['user_id'];
        echo "<div class='customer-info'>";
        echo "<h3>Customer: " . htmlspecialchars($row['full_name']) . "</h3>";
        echo "<p>Address: " . htmlspecialchars($row['address']) . "</p>";
        echo "<p>Phone: " . htmlspecialchars($row['phone']) . "</p>";
        
        // Download link
        echo '<a class="download-btn" href="download_sales_report.php?user_id=' . htmlspecialchars($row['user_id']) . '" target="_blank">Download Sales Report (CSV)</a>';
        echo "</div>"; // Close customer-info

        echo "<table>";
        echo "<tr><th>Sale ID</th><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Price</th><th>Total Amount</th><th>Date</th></tr>";
    }

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['sale_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sale_total']) . "</td>";
    echo "<td>" . htmlspecialchars($row['sale_date']) . "</td>";
    echo "</tr>";
}

echo "</table><br/>";
echo "</body></html>";

// Close the database connection
$conn->close();
?>
