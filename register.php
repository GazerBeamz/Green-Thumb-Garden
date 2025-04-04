<!-- filepath: c:\xampp\htdocs\greenthumb_garden\register.php -->
<?php
require_once 'includes/db.php';
require 'vendor/autoload.php'; // Include PHPMailer via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (firstname, middlename, lastname, email, password, role) 
                  VALUES ('$firstname', '$middlename', '$lastname', '$email', '$hashed_password', 'customer')";
        if (mysqli_query($conn, $query)) {
            // Send email with PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'brandonkylerojas1@gmail.com'; // Your Gmail address
                $mail->Password = 'yhtq gtsf byxj kyde'; // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('brandonkylerojas1@gmail.com', 'Green Thumb Garden');
                $mail->addAddress($email, $firstname); // Add recipient

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Welcome to Green Thumb Garden!';
                $mail->Body = "
                    <h3>Hello $firstname,</h3>
                    <p>Thank you for registering at Green Thumb Garden.</p>
                    <p>Your login details are:</p>
                    <ul>
                        <li><strong>Email:</strong> $email</li>
                        <li><strong>Password:</strong> $password</li>
                    </ul>
                    <p>Please keep this information secure.</p>
                    <p>Best regards,<br>Green Thumb Garden Team</p>
                ";

                $mail->send();
                header("Location: login.php?success=1");
            } catch (Exception $e) {
                $error = "Registration successful, but we couldn't send the email. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/register.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
</head>
<body class="register-body">
    <?php if (isset($error)) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: '<?php echo $error; ?>',
                confirmButtonColor: '#28a745'
            });
        </script>
    <?php } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($error)) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: 'Welcome to Green Thumb Garden! Please check your email for details.',
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location.href = 'login.php';
            });
        </script>
    <?php } ?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 register-card animate__animated animate__fadeInUp">
            <div class="text-center mb-4">
                <img src="assets/images/greenthumb_garden.png" alt="Green Thumb Logo" class="img-fluid mb-3 register-logo">
                <h3 class="fw-bold text-success">Create Your Account</h3>
                <p class="text-muted">Join Green Thumb Garden and start your gardening journey today!</p>
            </div>
            <form method="POST" onsubmit="return handleFormSubmit(event)">
                <?php if (isset($error)) echo "<p class='text-danger text-center mb-3 animate__animated animate__shakeX'>$error</p>"; ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" name="firstname" id="firstname" class="form-control" placeholder="John" required>
                    </div>
                    <div class="col-md-6">
                        <label for="middlename" class="form-label">Middle Name (Optional)</label>
                        <input type="text" name="middlename" id="middlename" class="form-control" placeholder="Michael">
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Doe" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="example@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter a strong password" required>
                            <span class="input-group-text bg-light border-start-0" id="togglePassword" style="cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                                    <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                            <span class="input-group-text bg-light border-start-0" id="toggleConfirmPassword" style="cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                                    <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2 mt-4">Register</button>
                <p class="mt-3 text-center text-muted">Already have an account? <a href="login.php" class="text-success fw-bold">Login</a></p>
            </form>
        </div>
    </div>
    <script src="assets/js/view-password.js"></script>
    <script src="assets/js/register-validation.js"></script>
</body>
</html>