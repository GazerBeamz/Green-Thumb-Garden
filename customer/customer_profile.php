<?php
require_once '../includes/db.php';

// Check if user is logged in and has customer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT firstname, lastname, username, email, profile_image FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "../assets/profiles/";
        $fileName = basename($_FILES['profile_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow only certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                $profileImage = $fileName;
            } else {
                $errorMessage = "Failed to upload image.";
            }
        } else {
            $errorMessage = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        $profileImage = $user['profile_image'];
    }

    // Update user details in the database
    $updateQuery = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', username = '$username', email = '$email', profile_image = '$profileImage' WHERE id = '$user_id'";
    if (mysqli_query($conn, $updateQuery)) {
        $successMessage = "Profile updated successfully!";
        // Refresh user details
        $query = "SELECT firstname, lastname, username, email, profile_image FROM users WHERE id = '$user_id'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $errorMessage = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/customer_profile.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-4 profile-sidebar">
                <div class="profile-image-wrapper">
                    <form method="POST" action="" enctype="multipart/form-data" id="profileForm">
                        <label for="profile_image">
                            <img src="../assets/profiles/<?php echo htmlspecialchars($user['profile_image'] ?: 'profile-placeholder.png'); ?>" 
                                 alt="Profile Picture" 
                                 id="profileImagePreview">
                            <span class="upload-icon"><i class="fas fa-camera"></i></span>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
                <h4><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>

            <!-- Profile Form -->
            <div class="col-md-8">
                <h3>Profile Settings</h3>
                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                    </div>
                <?php elseif (isset($errorMessage)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                    </div>
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success">Save Profile</button>
                    <a href="customer_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/customer-profilerendering.js"></script>
</body>

</html>