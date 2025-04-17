<?php
require_once '../includes/db.php';
include '../includes/sidebar.php'; // Corrected path

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch employees and customers
$employees = mysqli_query($conn, "SELECT * FROM users WHERE role = 'employee'");
$customers = mysqli_query($conn, "SELECT * FROM users WHERE role = 'customer'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
</head>

<body>
    <?php include '../includes/sidebar.php'; ?> <!-- Corrected path -->

    <!-- Main Content -->
    <main class="content">
        <header class="d-flex justify-content-between align-items-center py-3 px-4 shadow-sm">
            <h1 class="text-primary">Manage Users</h1>
        </header>

        <!-- Manage Employees -->
        <section id="manage-employees" class="py-4 px-4">
            <h4 class="section-title">Manage Employees</h4>
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">Add Employee</button>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($employees)) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= ucfirst($row['status'] ?? 'Unknown') ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="showActionModal(<?= $row['id'] ?>, 'deactivate')">Deactivate</button>
                                <button class="btn btn-success btn-sm" onclick="showActionModal(<?= $row['id'] ?>, 'activate')">Activate</button>
                                <button class="btn btn-danger btn-sm" onclick="showActionModal(<?= $row['id'] ?>, 'delete')">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Manage Customers -->
        <section id="manage-customers" class="py-4 px-4">
            <h4 class="section-title">Manage Customers</h4>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($customers)) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= ucfirst($row['status'] ?? 'Unknown') ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="showActionModal(<?= $row['id'] ?>, 'deactivate')">Deactivate</button>
                                <button class="btn btn-success btn-sm" onclick="showActionModal(<?= $row['id'] ?>, 'activate')">Activate</button>
                                <button class="btn btn-danger btn-sm" onclick="showActionModal(<?= $row['id'] ?>, 'delete')">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin-message.js"></script>
</body>

</html>

