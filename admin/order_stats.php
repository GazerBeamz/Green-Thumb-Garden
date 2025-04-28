<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '{$_SESSION['user_id']}'"));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
</head>

<body>
    <!-- Main Content -->
    <main class="content">
        <header class="d-flex justify-content-between align-items-center py-3 px-4 shadow-sm">
            <h1 class="text-primary">Analytics</h1>
            <div class="d-flex align-items-center gap-3">
                <a href="admin_dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </header>

        <!-- Analytics Section -->
        <section id="analytics" class="py-4 px-4">
            <div class="row g-4">
                <!-- Line Graph -->
                <div class="col-12">
                    <div class="card shadow-sm p-4 order-stats-card">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h6 class="chart-title mb-0">Order Growth</h6>
                            <div class="d-flex gap-2 chart-filters">
                                <select id="viewMode" class="custom-select">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                <select id="monthFilter" class="custom-select">
                                    <option value="jan">January 2025</option>
                                    <option value="feb">February 2025</option>
                                    <option value="mar">March 2025</option>
                                    <option value="apr" selected>April 2025</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="orderChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Recent Activity -->
                <div class="col-12">
                    <div class="card shadow-sm p-4 recent-activity-card">
                        <h6 class="chart-title">Recent Activity</h6>
                        <div class="activity-list">
                            <div class="activity-item">
                                <img src="../assets/icons/report-icon.png" alt="Report Icon" class="activity-icon">
                                <div class="activity-info">
                                    <span class="activity-user">Alice Brown</span>
                                    <span class="activity-action">Created a new report...</span>
                                    <span class="activity-time">2h ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <img src="../assets/icons/permissions-icon.png" alt="Permissions Icon" class="activity-icon">
                                <div class="activity-info">
                                    <span class="activity-user">Mark Thompson</span>
                                    <span class="activity-action">Updated user permissions...</span>
                                    <span class="activity-time">4h ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <img src="../assets/icons/delete-icon.png" alt="Delete Icon" class="activity-icon">
                                <div class="activity-info">
                                    <span class="activity-user">Linda Smith</span>
                                    <span class="activity-action">Deleted 3 spam comments...</span>
                                    <span class="activity-time">6h ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <img src="../assets/icons/login-icon.png" alt="Login Icon" class="activity-icon">
                                <div class="activity-info">
                                    <span class="activity-user">Robert Williams</span>
                                    <span class="activity-action">Logged in...</span>
                                    <span class="activity-time">8h ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/line-graph-chart.js"></script>
</body>

</html>