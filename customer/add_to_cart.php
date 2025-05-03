<?php
// Placeholder for session check (to be implemented later with backend)
$user_id = 1; // Mock user ID
$fullname = "John Doe"; // Mock user name
$profile_image = "profile-placeholder.png"; // Mock profile image
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="../assets/css/add_to_cart.css" rel="stylesheet">
    <link href="../assets/css/customer_dashboard.css" rel="stylesheet">
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo-container">
            <img src="../assets/images/greenthumb_garden.png" alt="Logo" class="img-fluid">
            <span class="logo-text">Green Thumb</span>
        </div>
        <a href="customer_dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
        <a href="add_to_cart.php" class="cart-link position-relative">
            <i class="bi bi-cart"></i> My Cart
            <span class="cart-count badge rounded-pill bg-danger">2</span>
        </a>
        <a href="#gardening_tips.php"><i class="bi bi-flower1"></i> Gardening Tips</a>
    </nav>

    <!-- Main Content -->
    <main class="content">
        <!-- Cart Section -->
        <section class="cart-section">
            <h4 class="section-title">Shopping Cart</h4>
            <div id="cart-items">
                <!-- Cart items will be dynamically populated here -->
            </div>
            <div class="cart-summary">
                <h5>Total: ₱<span id="cart-total">0.00</span></h5>
                <button class="btn btn-proceed-checkout mt-3" disabled>Proceed to Checkout</button>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/account-activation.js"></script>
    <script src="../functions/JS/addcart.js"></script>
</body>

</html>