<?php
require_once 'includes/db.php';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Password Reset Successful',
                text: 'Your password has been successfully reset. You can now log in.',
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['status'] === 'inactive') {
            $error = "Your account is inactive. Please contact the administrator.";
        } elseif (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'employee') {
                header("Location: employee/employee_dashboard.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: admin/admin_dashboard.php");
            } elseif ($user['role'] === 'customer') {
                header("Location: customer/customer_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body class="login-body">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 login-card animate__animated animate__fadeInUp">
            <div class="text-center mb-4">
                <img src="assets/images/greenthumb_garden.png" alt="Green Thumb Logo" class="img-fluid mb-3 login-logo">
                <h3 class="fw-bold text-success">Welcome Back!</h3>
                <p class="text-muted">Sign in to continue your gardening journey.</p>
            </div>
            <form method="POST">
                <?php if (isset($error)) echo "<p class='text-danger text-center mb-3 animate__animated animate__shakeX'>$error</p>"; ?>
                <?php if (isset($_GET['message']) && $_GET['message'] === 'Session Ended') : ?>
                    <div class="alert alert-warning text-center">
                        Session Ended
                    </div>
                <?php endif; ?>
                <div class="mb-4 position-relative">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-person" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                            </svg>
                        </span>
                        <input type="email" name="email" id="email" class="form-control border-start-0" placeholder="Enter your email" required>
                    </div>
                </div>
                <div class="mb-4 position-relative">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-lock" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Enter your password" required>
                        <span class="input-group-text bg-light border-start-0" id="togglePassword">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                                <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100 py-3 fw-semibold shadow-sm">Login</button>
                <div class="text-center mt-3">
                    <a href="forgot-password.php" class="forgot-password-link text-success fw-bold">Forgot Password?</a>
                </div>
                <p class="mt-4 text-center text-muted">New to Green Thumb? <a href="register.php" class="text-success fw-bold">Sign up here</a></p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/view-password.js"></script>
</body>

</html>