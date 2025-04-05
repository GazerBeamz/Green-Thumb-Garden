<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if the token is valid and not expired
        $query = "SELECT * FROM users WHERE reset_token='$token' AND reset_expires_at > NOW()";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // Update the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_expires_at=NULL WHERE id='{$user['id']}'";
            mysqli_query($conn, $query);

            $success = "Your password has been reset successfully.";
        } else {
            $error = "Invalid or expired token.";
        }
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    header("Location: forgot-password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 login-card">
            <h3 class="text-center mb-4">Reset Password</h3>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='text-success text-center'>$success</p>"; ?>
            <?php if (!isset($success)) { ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your new password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your new password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Reset Password</button>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>