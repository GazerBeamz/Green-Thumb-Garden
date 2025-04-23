<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['session_status' => 'inactive']);
    exit();
}

$userId = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT session_status FROM users WHERE id = $userId");
$user = mysqli_fetch_assoc($result);

echo json_encode(['session_status' => $user['session_status']]);