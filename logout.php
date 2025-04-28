<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    mysqli_query($conn, "INSERT INTO login_logs (user_id, type) VALUES ($userId, 'logout')");
}

session_destroy();
header("Location: login.php");
exit(); 