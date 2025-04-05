<?php
require_once 'includes/db.php';
require 'vendor/autoload.php'; // Include PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Store the token in the database
        $query = "UPDATE users SET reset_token='$token', reset_expires_at='$expires_at' WHERE email='$email'";
        mysqli_query($conn, $query);

        // Send the reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Your Gmail address
            $mail->Password = 'your-app-password'; // Your Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Green Thumb Garden');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <h3>Password Reset Request</h3>
                <p>Click the link below to reset your password:</p>
                <a href='http://localhost/greenthumb_garden/reset-password.php?token=$token'>Reset Password</a>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
            $success = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error = "Failed to send email. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 login-card">
            <h3 class="text-center mb-4">Forgot Password</h3>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='text-success text-center'>$success</p>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Send Reset Link</button>
            </form>
        </div>
    </div>
</body>
</html>