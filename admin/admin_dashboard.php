<?php
// Include database connection and authentication checks
require_once '../includes/db.php';
// Add authentication logic to ensure only admins can access this page
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container text-center py-4">
            <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
            <h4 class="logo-text mt-2">Admin Dashboard</h4>
        </div>
        <nav class="nav flex-column">
            <a href="#dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="#manage-employees" class="nav-link"><i class="fas fa-users"></i> Manage Employees</a>
            <a href="#manage-customers" class="nav-link"><i class="fas fa-user-friends"></i> Manage Customers</a>
            <a href="#messages" class="nav-link"><i class="fas fa-envelope"></i> Messages</a>
            <a href="#actions" class="nav-link"><i class="fas fa-tasks"></i> Actions</a>
            <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="content">
        <header class="d-flex justify-content-between align-items-center py-3 px-4 shadow-sm">
            <h1 class="text-primary">Welcome, Admin</h1>
            <div class="d-flex align-items-center gap-3">
                <!-- Profile Container -->
                <div class="profile-container">
                    <img src="../assets/profiles/<?php echo htmlspecialchars($user['profile_image'] ?: 'profile-placeholder.png'); ?>" alt="Admin Profile" class="profile-img">
                    <div class="profile-hover">
                        <p>Admin Name</p>
                        <a href="admin_profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Overview -->
        <section id="dashboard" class="py-4 px-4">
            <h4 class="section-title">Dashboard Overview</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h6>Total Employees</h6>
                        <p class="display-6 text-primary">50</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h6>Total Customers</h6>
                        <p class="display-6 text-success">200</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h6>Messages</h6>
                        <p class="display-6 text-warning">10</p>
                    </div>
                </div>
            </div>
        </section>

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Fetch and display employees from the database -->
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM employees");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td>
                                    <button class='btn btn-danger btn-sm' onclick='removeEmployee({$row['id']})'>Remove</button>
                                </td>
                              </tr>";
                    }
                    ?>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Fetch and display customers from the database -->
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM customers");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td>
                                    <button class='btn btn-danger btn-sm' onclick='removeCustomer({$row['id']})'>Remove</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Messages -->
        <section id="messages" class="py-4 px-4">
            <h4 class="section-title">Messages</h4>
            <textarea class="form-control mb-3" rows="5" placeholder="Write a message..."></textarea>
            <button class="btn btn-primary">Send Message</button>
        </section>

        <!-- Actions -->
        <section id="actions" class="py-4 px-4">
            <h4 class="section-title">Oversee Actions</h4>
            <p>View logs and monitor activities here.</p>
        </section>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="add_employee.php">
                        <div class="mb-3">
                            <label for="employeeName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="employeeName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="employeeEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="employeeEmail" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        function removeEmployee(id) {
            if (confirm('Are you sure you want to remove this employee?')) {
                window.location.href = `remove_employee.php?id=${id}`;
            }
        }

        function removeCustomer(id) {
            if (confirm('Are you sure you want to remove this customer?')) {
                window.location.href = `remove_customer.php?id=${id}`;
            }
        }
    </script>
</body>

</html>