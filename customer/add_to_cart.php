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
            <span class="cart-count badge rounded-pill bg-danger">0</span>
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
            <div id="empty-cart-message" class="text-center py-5" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-cart-x mb-4" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 6H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 15H4a.5.5 0 0 1-.491-.408L1.01 1.607 0 1.5ZM3.14 6l1.25 6.5h8.22l1.25-6.5H3.14ZM5 12a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm7 1a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM11.354 4.354a.5.5 0 0 0-.708-.708L9.5 4.793 8.354 3.646a.5.5 0 1 0-.708.708L8.793 5.5 7.646 6.646a.5.5 0 0 0 .708.708L9.5 6.207l1.146 1.147a.5.5 0 0 0 .708-.708L10.207 5.5l1.147-1.146Z"/>
                </svg>
                <h5>Your cart is empty!</h5>
                <p class="text-muted">Looks like you haven't added anything to your cart yet.</p>
                <a href="customer_dashboard.php" class="btn btn-success mt-3">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
            </div>
            <div class="cart-summary" style="display: none;">
                <h5>Total: ₱<span id="cart-total">0.00</span></h5>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/account-activation.js"></script>
    <script src="../functions/JS/addcart.js"></script>
</body>

</html>