<?php
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT CONCAT(firstname, ' ', lastname) AS fullname, address, contact FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'status' => 'success',
        'fullname' => $row['fullname'],
        'address' => $row['address'],
        'contact' => $row['contact']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Customer not found.']);
}