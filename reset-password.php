<?php
date_default_timezone_set('UTC');
require_once 'includes/db.php';

// Get the MySQL server's timezone (SYSTEM timezone)
$result = mysqli_query($conn, "SELECT @@system_time_zone AS system_timezone");
$row = mysqli_fetch_assoc($result);
$mysql_timezone = $row['system_timezone'] ?? 'UTC'; // Fallback to UTC if not found

// Map Windows timezone names to PHP-compatible timezone names
$timezone_mapping = [
    'Romance Standard Time' => 'Europe/Paris',
    'Pacific Standard Time' => 'America/Los_Angeles',
    'Eastern Standard Time' => 'America/New_York',
    // Add more mappings as needed
];
$mysql_timezone = $timezone_mapping[$mysql_timezone] ?? $mysql_timezone;
error_log("MySQL system timezone: $mysql_timezone");

// Function to convert a MySQL SYSTEM timezone time to UTC for comparison
function mysql_timezone_to_utc($mysql_time, $mysql_timezone)
{
    $date = new DateTime($mysql_time, new DateTimeZone($mysql_timezone));
    $date->setTimezone(new DateTimeZone('UTC'));
    return $date->format('Y-m-d H:i:s');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Fetch the user and convert reset_expires_at to UTC for comparison
        $query = "SELECT * FROM users WHERE reset_token=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $current_time = date('Y-m-d H:i:s'); // Current time in UTC
        if ($user) {
            $reset_expires_at_utc = mysql_timezone_to_utc($user['reset_expires_at'], $mysql_timezone);
            error_log("Current time (PHP, UTC): $current_time");
            error_log("Stored reset_expires_at (MySQL timezone): " . $user['reset_expires_at']);
            error_log("Stored reset_expires_at (converted to UTC): $reset_expires_at_utc");

            if ($reset_expires_at_utc > $current_time) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $update_query = "UPDATE users SET password=?, reset_token=NULL, reset_expires_at=NULL WHERE id=?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param('si', $hashed_password, $user['id']);
                $update_stmt->execute();

                // Redirect to login page after successful password reset
                header("Location: login.php?success=1");
                exit;
            } else {
                $error = "Invalid or expired token.";
            }
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
    <link href="assets/css/reset_password.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h3>Reset Password</h3>
        <?php if (isset($error)) echo "<p class='alert alert-danger'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='alert alert-success'>$success</p>"; ?>
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
</body>

</html>