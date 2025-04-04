<?php
require_once '../includes/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Admin Dashboard</h1>
        <div class="row mt-4">
            <div class="col-md-6">
                <a href="manage_users.php" class="btn btn-success w-100">Manage Users</a>
            </div>
            <div class="col-md-6">
                <a href="../logout.php" class="btn btn-danger w-100">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>