<?php
require_once '../includes/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_employee') {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (firstname, lastname, email, password, role) VALUES ('$firstname', '$lastname', '$email', '$hashedPassword', 'employee')";
    if (mysqli_query($conn, $query)) {
        $successMessage = "Employee created successfully!";
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'brandonkylerojas1@gmail.com';
            $mail->Password = 'yhtq gtsf byxj kyde';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('your-email@gmail.com', 'Green Thumb Garden');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Green Thumb Garden';
            $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #ffffff;'>
                        <div style='text-align: center; padding: 20px; background-color: #007bff; color: #ffffff; border-radius: 10px 10px 0 0;'>
                            <h2 style='margin: 0;'>Welcome to Green Thumb Garden</h2>
                        </div>
                        <div style='padding: 20px; text-align: left;'>
                            <p style='font-size: 16px; color: #333;'>Hello $firstname $lastname,</p>
                            <p style='font-size: 16px; color: #333;'>You have been added as an employee. Your credentials:</p>
                            <p style='font-size: 16px; color: #333;'><strong>Email:</strong> $email</p>
                            <p style='font-size: 16px; color: #333;'><strong>Password:</strong> $password</p>
                            <p style='font-size: 14px; color: #666;'>Please log in and change your password ASAP.</p>
                        </div>
                    </div>
                ";
            $mail->send();
        } catch (Exception $e) {
            $errorMessage = "Employee created, but email failed: " . $mail->ErrorInfo;
        }
    } else {
        $errorMessage = "Failed to create employee: " . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['deactivate', 'activate', 'delete'])) {
    $userId = intval($_POST['user_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason'] ?? '');

    if ($_POST['action'] === 'deactivate') {
        $query = "UPDATE users SET status = 'inactive', session_status = 'inactive', deactivation_reason = '$reason' WHERE id = $userId";
        if (mysqli_query($conn, $query)) {
            // Send a message to the user
            $message = "Your account has been deactivated. Reason: $reason";
            $query = "INSERT INTO messages (sender_id, recipient_id, message, is_read) VALUES ({$_SESSION['user_id']}, $userId, '$message', 0)";
            mysqli_query($conn, $query);

            // Fetch the user's email
            $result = mysqli_query($conn, "SELECT email FROM users WHERE id = $userId");
            $user = mysqli_fetch_assoc($result);
            $email = $user['email'];

            // Send an email notification
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'brandonkylerojas1@gmail.com';
                $mail->Password = 'yhtq gtsf byxj kyde';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('your-email@gmail.com', 'Green Thumb Garden');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Account Deactivation Notification';
                $mail->Body = "<p>Your account has been deactivated. Reason: $reason</p>";
                $mail->send();
            } catch (Exception $e) {
                $errorMessage = "Account deactivated, but email failed: " . $mail->ErrorInfo;
            }

            $successMessage = "Account deactivated successfully!";
        } else {
            $errorMessage = "Failed to deactivate account: " . mysqli_error($conn);
        }
    } elseif ($_POST['action'] === 'activate') {
        $query = "UPDATE users SET status = 'active', session_status = 'active', deactivation_reason = NULL WHERE id = $userId";
        if (mysqli_query($conn, $query)) {
            $successMessage = "Account activated successfully!";
        } else {
            $errorMessage = "Failed to activate account: " . mysqli_error($conn);
        }
    } elseif ($_POST['action'] === 'delete') {
        $result = mysqli_query($conn, "SELECT email FROM users WHERE id = $userId");
        $user = mysqli_fetch_assoc($result);
        $email = $user['email'];

        $query = "DELETE FROM users WHERE id = $userId";
        if (mysqli_query($conn, $query)) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'brandonkylerojas1@gmail.com';
                $mail->Password = 'yhtq gtsf byxj kyde';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('your-email@gmail.com', 'Green Thumb Garden');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Account Deletion Notification';
                $mail->Body = "<p>Your account has been deleted. Reason: $reason</p>";
                $mail->send();
            } catch (Exception $e) {
                $errorMessage = "Account deleted, but email failed: " . $mail->ErrorInfo;
            }
            $successMessage = "Account deleted successfully!";
        } else {
            $errorMessage = "Failed to delete account: " . mysqli_error($conn);
        }
    }

    if (!empty($reason) && in_array($_POST['action'], ['deactivate', 'delete'])) {
        $logQuery = "INSERT INTO account_logs (user_id, action, reason) VALUES ($userId, '{$_POST['action']}', '$reason')";
        mysqli_query($conn, $logQuery);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'activate') {
    $userId = intval($_POST['user_id']);
    $query = "UPDATE users SET status = 'active', session_status = 'active', deactivation_reason = NULL WHERE id = $userId";
    if (mysqli_query($conn, $query)) {
        echo "User activated successfully!";
    } else {
        echo "Failed to activate user: " . mysqli_error($conn);
    }
    exit();
}

$query = "
    SELECT 
        account_logs.id AS log_id,
        account_logs.action,
        account_logs.reason,
        account_logs.created_at,
        CONCAT(users.firstname, ' ', users.lastname) AS user_account,
        users.role
    FROM 
        account_logs
    INNER JOIN 
        users 
    ON 
        account_logs.user_id = users.id
    ORDER BY 
        account_logs.created_at DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-container text-center py-4">
            <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
            <h4 class="logo-text mt-2">Admin Dashboard</h4>
        </div>
        <nav>
            <ul class="nav flex-column">
                <li><a href="admin_dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage_users.php" class="nav-link"><i class="fas fa-user-friends"></i> Manage Users</a></li>
                <li><a href="#actions" class="nav-link"><i class="fas fa-tasks"></i> Actions</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="content">
        <header class="d-flex justify-content-between align-items-center py-3 px-4 shadow-sm">
            <h1 class="text-primary">Welcome, <?php echo htmlspecialchars($user['firstname']); ?></h1>
            <div class="d-flex align-items-center gap-3">
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
        </section>


        <section id="manage-employees" class="py-4 px-4">
            <h4 class="section-title">Manage Employees</h4>
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">Add Employee</button>
            <?php if (isset($successMessage)) echo "<p class='alert alert-success'>$successMessage</p>"; ?>
            <?php if (isset($errorMessage)) echo "<p class='alert alert-danger'>$errorMessage</p>"; ?>
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
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM users WHERE role = 'employee'");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status = isset($row['status']) ? ucfirst($row['status']) : 'Unknown';
                        echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['firstname']} {$row['lastname']}</td>
                                    <td>{$row['email']}</td>
                                    <td>$status</td>
                                    <td>
                                        <button 
                                            class='btn btn-warning btn-sm' 
                                            onclick='showActionModal({$row['id']}, \"deactivate\")'
                                        >
                                            Deactivate
                                        </button>
                                        <button 
                                            class='btn btn-success btn-sm' 
                                            onclick='activateUser({$row['id']}, \"$status\")'
                                        >
                                            Activate
                                        </button>
                                        <button 
                                            class='btn btn-danger btn-sm' 
                                            onclick='showActionModal({$row['id']}, \"delete\")'
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>


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
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM users WHERE role = 'customer'");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status = isset($row['status']) ? ucfirst($row['status']) : 'Unknown';
                        echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['firstname']} {$row['lastname']}</td>
                                    <td>{$row['email']}</td>
                                    <td>$status</td>
                                    <td>
                                        <button class='btn btn-warning btn-sm' onclick='showActionModal({$row['id']}, \"deactivate\")'>Deactivate</button>
                                        <button class='btn btn-success btn-sm' onclick='showActionModal({$row['id']}, \"activate\")'>Activate</button>
                                        <button class='btn btn-danger btn-sm' onclick='showActionModal({$row['id']}, \"delete\")'>Delete</button>
                                    </td>
                                </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <section id="account-logs" class="py-4 px-4">
            <h4 class="section-title">Account Logs</h4>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Log ID</th>
                        <th>User Account</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Reason</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "
                        SELECT 
                            account_logs.id AS log_id,
                            account_logs.action,
                            account_logs.reason,
                            account_logs.created_at,
                            CONCAT(users.firstname, ' ', users.lastname) AS user_account,
                            users.role
                        FROM 
                            account_logs
                        INNER JOIN 
                            users 
                        ON 
                            account_logs.user_id = users.id
                        ORDER BY 
                            account_logs.created_at DESC
                    ";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['log_id']}</td>
                            <td>" . htmlspecialchars($row['user_account']) . "</td>
                            <td>" . ucfirst(htmlspecialchars($row['role'])) . "</td>
                            <td>" . htmlspecialchars($row['action']) . "</td>
                            <td>" . htmlspecialchars($row['reason']) . "</td>
                            <td>" . htmlspecialchars($row['created_at']) . "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create_employee">
                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success">Create Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel">Account Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="actionForm">
                        <input type="hidden" name="user_id" id="actionUserId">
                        <input type="hidden" name="action" id="actionType">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason (optional)</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/profile-dropdown.js"></script>
    <script>
        // Pass the user ID to JavaScript
        window.currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
        document.addEventListener("DOMContentLoaded", () => {
            setInterval(() => {
                fetch('../check_session.php') // Updated path
                    .then(response => response.json())
                    .then(data => {
                        if (data.session_status === 'inactive') {
                            alert('Session Ended');
                            window.location.href = '../login.php?message=Session Ended'; // Ensure the correct path to login.php
                        }
                    })  
                    .catch(error => console.error('Error checking session:', error));
            }, 5000); // Check every 5 seconds
        });
    </script>
    <script src="../assets/js/admin-message.js"></script>
    <script src="../assets/js/account-activation.js"></script>
</body>

</html>