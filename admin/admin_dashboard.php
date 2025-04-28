<?php
require_once '../includes/db.php';
include 'sidebar.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '{$_SESSION['user_id']}'"));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['ajax'])) {
    $recipientId = intval($_POST['recipient_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $senderId = $_SESSION['user_id'];

    $query = "INSERT INTO messages (sender_id, recipient_id, message, is_read) VALUES ($senderId, $recipientId, '$message', 0)";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => htmlspecialchars($message), 'isAdmin' => true]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'activate') {
    $userId = intval($_POST['user_id']);
    $query = "UPDATE users SET status = 'active', session_status = 'active', deactivation_reason = NULL WHERE id = $userId";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'User activated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to activate user: ' . mysqli_error($conn)]);
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
</head>

<body>
    <!-- Main Content -->
    <main class="content">
        <header class="d-flex justify-content-between align-items-center py-3 px-4 shadow-sm">
            <h1 class="text-primary">Welcome, <?php echo htmlspecialchars($user['firstname']); ?></h1>
            <div class="d-flex align-items-center gap-3">
                <!-- Analytics Icon (Visible on Small Screens) -->
                <a href="order_stats.php" class="btn btn-primary d-lg-none analytics-icon">
                    <i class="fas fa-chart-line"></i>
                </a>
                <div id="chat-head" class="chat-head position-static">
                    <button id="chat-toggle" class="btn btn-primary position-relative">
                        <i class="fas fa-comments"></i>
                        <span id="chat-notification" class="chat-notification"></span>
                    </button>
                    <div id="chat-list" class="chat-list d-none">
                        <div class="chat-header">
                            <h6>Messages</h6>
                            <button id="chat-close" class="btn btn-sm btn-danger">×</button>
                        </div>
                        <div id="chat-employees" class="chat-employees">
                            <!-- Employee list will be dynamically loaded here -->
                        </div>
                    </div>

                    <div id="chat-box" class="chat-box d-none">
                        <div class="chat-header">
                            <button id="chat-back" class="btn btn-sm btn-secondary me-2">Back</button>
                            <button id="chat-close-box" class="btn btn-sm btn-danger">×</button>
                        </div>
                        <div id="chat-messages" class="chat-messages">
                            <!-- Messages will be dynamically loaded here -->
                        </div>
                        <form id="chat-form" method="POST">
                            <input type="hidden" name="recipient_id" id="recipient-id">
                            <input type="hidden" name="ajax" value="1">
                            <div class="input-group">
                                <input type="text" name="message" id="chat-input" class="form-control" placeholder="Type a message..." required>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="profile-container">
                    <img src="../assets/profiles/<?php echo htmlspecialchars($user['profile_image'] ?: 'profile-placeholder.png'); ?>" alt="Admin Profile" class="profile-img">
                    <div class="profile-hover d-none">
                        <p><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></p>
                        <a href="../profile.php"><i class="fas fa-user"></i> My Profile</a>
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
                        <p class="display-6 text-primary">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'employee'");
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h6>Total Customers</h6>
                        <p class="display-6 text-success">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h6>Messages</h6>
                        <p class="display-6 text-warning">10</p>
                    </div>
                </div>
            </div>

            <!-- Order Growth Line Graph and Recent Activity -->
            <div class="row g-4 mt-4" id="analytics-section">
                <!-- Line Graph -->
                <div class="col-lg-8 col-md-12">
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
                <div class="col-lg-4 col-md-12">
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
    <script src="../assets/js/profile-dropdown.js"></script>
    <script>
        window.currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
        document.addEventListener("DOMContentLoaded", () => {
            setInterval(() => {
                fetch('../check_session.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.session_status === 'inactive') {
                            alert('Session Ended');
                            window.location.href = '../login.php?message=Session Ended';
                        }
                    })
                    .catch(error => console.error('Error checking session:', error));
            }, 5000);
        });
    </script>
    <script src="../assets/js/admin-message.js"></script>
    <script src="../assets/js/line-graph-chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const activityList = document.querySelector(".activity-list");

            // Fetch recent activities
            function fetchRecentActivities() {
                fetch("fetch_recent_activity.php")
                    .then(response => response.json())
                    .then(data => {
                        activityList.innerHTML = ""; // Clear the list
                        data.forEach(activity => {
                            const activityItem = document.createElement("div");
                            activityItem.className = "activity-item";
                            activityItem.innerHTML = `
                                <img src="../assets/profiles/${activity.profile_image}" alt="Profile Icon" class="activity-icon">
                                <div class="activity-info">
                                    <span class="activity-user">${activity.firstname} ${activity.lastname}</span>
                                    <span class="activity-action">${activity.type === 'login' ? 'Logged in' : 'Logged out'}</span>
                                    <span class="activity-time">${activity.time}</span>
                                </div>
                            `;
                            activityList.appendChild(activityItem);
                        });
                    })
                    .catch(error => console.error("Error fetching recent activities:", error));
            }

            // Fetch activities every 10 seconds
            fetchRecentActivities();
            setInterval(fetchRecentActivities, 10000);
        });
    </script>
</body>

</html>