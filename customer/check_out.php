<?php
require_once '../includes/db.php';

// Check if user is logged in and has customer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}
// Fetch user details
$user_id = $_SESSION['user_id'];
$user_query = "SELECT firstname, lastname, profile_image FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);

if ($user_row = mysqli_fetch_assoc($user_result)) {
    $fullname = htmlspecialchars($user_row['firstname'] . ' ' . $user_row['lastname']);
    $profile_image = htmlspecialchars($user_row['profile_image'] ?: 'profile-placeholder.png');
} else {
    $fullname = "Guest";
    $profile_image = "profile-placeholder.png";
}

// Fetch product details
if (!isset($_GET['product_id'])) {
    echo "<p class='text-danger'>Product not found. Please go back and select a product.</p>";
    exit();
}

$product_id = intval($_GET['product_id']);
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $productName = htmlspecialchars($row['name']);
    $productPriceRaw = $row['price'];
    $productPriceFormatted = number_format($productPriceRaw, 2);
    $productCategory = htmlspecialchars($row['category']);
    $productImage = htmlspecialchars($row['image']);
    $productDescription = htmlspecialchars($row['description']);
    $productStock = $row['stock'];
    $initialPrice = number_format($productPriceRaw, 2);
} else {
    echo "<p class='text-danger'>Product not found. Please go back and select a product.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="../assets/css/check_out.css" rel="stylesheet">
    <link href="../assets/css/customer_dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="hamburger"><i class="bi bi-list"></i></button>
                <h1 class="animate__animated animate__fadeIn">Explore Your Gardening Journey</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="profile-container">
                    <div class="profile-icon">
                        <img src="../assets/profiles/<?php echo $profile_image; ?>" alt="Profile" class="rounded-circle">
                    </div>
                    <div class="profile-hover d-none">
                        <p class="fw-bold"><?php echo htmlspecialchars($fullname); ?></p>
                        <a href="../profile.php"><i class="bi bi-pencil-square"></i> My Profile</a>
                        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>
        <!-- Checkout Section -->
        <section class="checkout-section">
            <!-- Go Back Button -->
            <div class="mb-4">
                <a href="customer_dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
            <div class="row g-4 justify-content-evenly">
                <!-- First Column: Order Summary -->
                <div class="col-lg-5">
                    <h4 class="section-title">Order Summary</h4>
                    <div class="order-summary">
                        <div class="product-details d-flex align-items-center gap-3">
                            <img src="../assets/products/<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" class="product-image">
                            <div class="product-info flex-grow-1">
                                <h5 class="product-name"><?php echo $productName; ?></h5>
                                <p class="product-description">Category: <?php echo $productCategory; ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <p class="product-price">₱<span id="base-price"><?php echo $productPriceRaw; ?></span></p>
                                    <div class="quantity-selector d-flex align-items-center gap-2">
                                        <button id="decrease-quantity" class="btn btn-sm btn-outline-secondary">-</button>
                                        <input type="number" id="quantity" class="form-control quantity-input" value="1" min="1" readonly>
                                        <button id="increase-quantity" class="btn btn-sm btn-outline-secondary">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Additional order summary details -->
                        <div class="order-details mt-4">
                            <p class="product-description"><?php echo $productDescription; ?></p>
                        </div>
                        <!-- Hidden inputs for JavaScript -->
                        <input type="hidden" id="product-price" value="<?php echo $productPriceRaw; ?>">
                        <input type="hidden" id="product-id" value="<?php echo $product_id; ?>">
                        <input type="hidden" id="product-category" value="<?php echo $productCategory; ?>">
                    </div>
                </div>

                <!-- Second Column: Customer Details -->
                <div class="col-lg-6">
                    <h4 class="section-title">Customer Details</h4>
                    <div id="customer-details" class="customer-details">
                        <div class="card-body">
                            <div class="mb-3 text-start d-flex align-items-center">
                                <label class="form-label me-3" style="width: 150px;">Full Name</label>
                                <input type="text" class="form-control" id="fullname-input" value="Loading..." readonly>
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label class="form-label me-3" style="width: 150px;">Contact Number</label>
                                <input type="text" class="form-control" id="contact-input" value="Loading..." readonly>
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label class="form-label me-3" style="width: 150px;">Shipping Address</label>
                                <input type="text" class="form-control" id="address-input" value="Loading..." readonly>
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label class="form-label me-3" style="width: 150px;">Mode of Payment</label>
                                <select id="payment-method" class="form-select">
                                    <option value="">Select payment method</option>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>
                            <div class="total-section mt-4">
                                <p class="total-text">Total: <span class="total-amount">₱<span id="total-price"><?php echo $initialPrice; ?></span></span></p>
                            </div>
                            <div class="action-buttons mt-4 d-flex gap-3">
                                <button class="btn btn-add-to-cart w-50" data-product-id="<?php echo $product_id; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                                <button class="btn btn-checkout w-50" data-user-id="<?php echo $user_id ?>" data-product-id="<?php echo $product_id; ?>">
                                    <i class="bi bi-credit-card"></i> Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../functions/JS/checkout.js"></script>
    <script src="../functions/JS/cartcounter.js"></script>
    <script src="../assets/js/profile-dropdown.js"></script>
    <script src="../assets/js/account-activation.js"></script>
</body>
</html>