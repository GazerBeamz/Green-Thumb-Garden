<?php
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the cart ID from the request
$data = json_decode(file_get_contents("php://input"), true);
$cart_id = intval($data['cart_id']);

// Delete the cart item
$query = "DELETE FROM cart WHERE id = '$cart_id' AND customer_id = '$user_id'";
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Product removed from cart']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove product: ' . mysqli_error($conn)]);
}
?>