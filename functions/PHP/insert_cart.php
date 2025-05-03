<?php
require_once '../../includes/db.php';

header('Content-Type: application/json');

// Suppress warnings and notices
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['product_id']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

$customer_id = $_SESSION['user_id'];
$product_id = intval($input['product_id']);
$product_name = $conn->real_escape_string($input['product_name'] ?? '');
$product_description = $conn->real_escape_string($input['product_description'] ?? '');
$product_category = $conn->real_escape_string($input['product_category'] ?? '');
$product_price = floatval($input['product_price'] ?? 0);
$quantity = intval($input['quantity']);

// Validate input
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// Insert into cart
$query = "INSERT INTO cart (customer_id, product_id, product_name, product_description, product_category, product_price, quantity) 
          VALUES (?, ?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), product_price = VALUES(product_price)";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("iisssdi", $customer_id, $product_id, $product_name, $product_description, $product_category, $product_price, $quantity);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
?>