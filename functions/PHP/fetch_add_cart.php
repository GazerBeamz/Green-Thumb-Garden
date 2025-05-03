<?php
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user with product image
$query = "SELECT c.id AS cart_id, c.customer_id, c.product_id, c.product_name AS name, 
                 c.product_description, c.product_category, c.product_price AS price, 
                 c.quantity, p.image 
          FROM cart c 
          LEFT JOIN products p ON c.product_id = p.id 
          WHERE c.customer_id = '$user_id'";
$result = mysqli_query($conn, $query);

$cart_items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = [
            'cart_id' => $row['cart_id'],
            'name' => htmlspecialchars($row['name']),
            'image' => htmlspecialchars($row['image'] ?: 'placeholder.jpg'), // Fallback image if not found
            'price' => number_format($row['price'], 2),
            'quantity' => $row['quantity'],
            'total' => number_format($row['price'] * $row['quantity'], 2),
            'product_category' => htmlspecialchars($row['product_category']),
        ];
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . mysqli_error($conn)]);
    exit();
}

echo json_encode(['status' => 'success', 'cart_items' => $cart_items]);
?>