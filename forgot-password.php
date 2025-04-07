<?php
date_default_timezone_set('UTC'); // Ensure PHP is in UTC
require_once 'includes/db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Function to convert a UTC time to the MySQL SYSTEM timezone for storage
function utc_to_mysql_timezone($utc_time, $mysql_timezone)
{
    $date = new DateTime($utc_time, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone($mysql_timezone));
    return $date->format('Y-m-d H:i:s');
}

// Function to convert a MySQL SYSTEM timezone time to UTC for comparison
function mysql_timezone_to_utc($mysql_time, $mysql_timezone)
{
    $date = new DateTime($mysql_time, new DateTimeZone($mysql_timezone));
    $date->setTimezone(new DateTimeZone('UTC'));
    return $date->format('Y-m-d H:i:s');
}

$showOtpModal = false;

// Handle sending a new OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_otp') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Always generate a new OTP
        $otp = random_int(100000, 999999);
        $expires_at = gmdate('Y-m-d H:i:s', strtotime('+10 minutes')); // Generate expiration in UTC
        $expires_at_mysql = utc_to_mysql_timezone($expires_at, $mysql_timezone);
        error_log("Storing reset_expires_at (UTC): $expires_at");
        error_log("Storing reset_expires_at (MySQL timezone): $expires_at_mysql");

        $query = "UPDATE users SET reset_otp='$otp', reset_expires_at='$expires_at_mysql' WHERE email='$email'";
        if (!mysqli_query($conn, $query)) {
            error_log("Failed to update OTP: " . mysqli_error($conn));
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'brandonkylerojas1@gmail.com';
            $mail->Password = 'yhtq gtsf byxj kyde';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('brandonkylerojas1@gmail.com', 'Green Thumb Garden');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #ffffff;'>
        <div style='text-align: center; padding: 20px; background-color: #007bff; color: #ffffff; border-radius: 10px 10px 0 0;'>
            <h2 style='margin: 0;'>Password Reset Code</h2>
        </div>
        <div style='padding: 20px; text-align: center;'>
            <p style='font-size: 16px; color: #333;'>Hello,</p>
            <p style='font-size: 16px; color: #333;'>You have requested to reset your password. Please use the following OTP code to complete your password reset:</p>
            <h1 style='font-size: 36px; color: #007bff; margin: 20px 0;'>$otp</h1>
            <p style='font-size: 14px; color: #666;'>Note: Do not share this code with others. If you did not request this password reset, please ignore this email.</p>
        </div>
    </div>
";

            $mail->send();
            $success = "An OTP has been sent to your email.";
            $showOtpModal = true;
        } catch (Exception $e) {
            $error = "Failed to send email. Please try again.";
            error_log("PHPMailer error: " . $mail->ErrorInfo);
        }
    } else {
        $error = "No account found with that email address.";
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_otp') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $otp = mysqli_real_escape_string($conn, $_POST['otp']); // Use the concatenated OTP string

    // Fetch the user and validate the OTP
    $query = "SELECT * FROM users WHERE email=? AND reset_otp=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $email, $otp); // Ensure the OTP is passed as a string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // OTP is valid, proceed with further logic
    } else {
        $otp_error = "Invalid OTP.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_otp') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $otp = $_POST['otp'];

    // Fetch the user and convert reset_expires_at to UTC for comparison
    $query = "SELECT * FROM users WHERE email=? AND reset_otp=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $email, $otp);
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
            // Generate a reset token for the password reset page
            $reset_token = bin2hex(random_bytes(16)); // Generate a secure token
            $token_expires_at = gmdate('Y-m-d H:i:s', strtotime('+10 minutes')); // Token expires in 10 minutes
            $token_expires_at_mysql = utc_to_mysql_timezone($token_expires_at, $mysql_timezone);

            // Store the reset_token in the correct column (reset_token, not reset_otp)
            $query = "UPDATE users SET reset_token='$reset_token', reset_expires_at='$token_expires_at_mysql', reset_otp=NULL WHERE email='$email'";
            if (!mysqli_query($conn, $query)) {
                error_log("Failed to store reset token: " . mysqli_error($conn));
            }

            header("Location: reset-password.php?token=$reset_token");
            exit;
        } else {
            $otp_error = "OTP has expired.";
        }
    } else {
        $otp_error = "Invalid OTP.";
    }

    error_log("OTP validation failed. Current time (PHP, UTC): $current_time");
    error_log("Stored reset_expires_at (MySQL timezone): " . ($user['reset_expires_at'] ?? 'No user found'));
    error_log("Stored reset_expires_at (converted to UTC): " . ($reset_expires_at_utc ?? 'N/A'));
    $showOtpModal = true; // Keep the modal open if OTP validation fails
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/forgot_password.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="shadow-lg p-4 login-card">
            <h3 class="text-center mb-4">Forgot Password</h3>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='text-success text-center'>$success</p>"; ?>
            <form method="POST" onsubmit="return validateEmail() && disableButton(this)">
                <input type="hidden" name="action" value="send_otp">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn btn-success w-100" id="sendOtpButton">Send OTP</button>
            </form>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade <?php echo $showOtpModal ? 'show' : ''; ?>" id="otpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="otpModalLabel" aria-hidden="<?php echo $showOtpModal ? 'false' : 'true'; ?>" style="<?php echo $showOtpModal ? 'display: block;' : ''; ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content otp-modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-center w-100" id="otpModalLabel">Enter OTP Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-3">Enter the 6-digit code sent to your email</p>
                    <form method="POST" onsubmit="return combineOtpInputs()">
                        <input type="hidden" name="action" value="verify_otp">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <input type="hidden" id="otp" name="otp" value="">
                        <div class="otp-input-container">
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                            <input type="text" maxlength="1" class="otp-input" required>
                        </div>
                        <p class="text-muted mt-2">Please check your email for the OTP code.</p>
                        <?php if (isset($otp_error)) echo "<p class='text-danger'>$otp_error</p>"; ?>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Verify OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/otp-verificationcode.js"></script>
</body>


</html>