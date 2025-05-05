<?php
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT COUNT(*) AS cart_count FROM cart WHERE customer_id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['status' => 'success', 'cart_count' => $row['cart_count']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch cart count']);
}
?>