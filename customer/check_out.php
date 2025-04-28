<?php
require_once '../includes/db.php';

// Check if user is logged in and has customer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT firstname, lastname, profile_image FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$fullname = $user['firstname'] . ' ' . $user['lastname'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="../assets/css/customer_dashboard.css" rel="stylesheet">
    <link href="../assets/css/check_out.css" rel="stylesheet">
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/checkout.js"></script>


</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo-container">
            <img src="../assets/images/greenthumb_garden.png" alt="Logo" class="img-fluid">
            <span class="logo-text">Green Thumb</span>
        </div>
        <a href="customer_dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
        <a href="#orders.php"><i class="bi bi-cart"></i> My Orders</a>
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
                        <img src="../assets/profiles/<?php echo htmlspecialchars($user['profile_image'] ?: 'profile-placeholder.png'); ?>" alt="Profile" class="rounded-circle">
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
            <div class="row g-4">
                <!-- First Column: Product Details -->
                <div class="col-lg-5">
                    <h4 class="section-title text-success">Product Details</h4>
                    <div class="card product-details-card shadow-sm border-0">
                        <?php
                        if (!isset($_GET['product_id'])) {
                            echo "<p class='text-danger'>Product not found. Please go back and select a product.</p>";
                            exit();
                        }

                        $product_id = intval($_GET['product_id']);
                        $query = "SELECT * FROM products WHERE id = $product_id";
                        $result = mysqli_query($conn, $query);

                        if ($row = mysqli_fetch_assoc($result)) {
                            $productName = htmlspecialchars($row['name']);
                            $productPriceRaw = $row['price']; // Get the raw price value from the database
                            $productPriceFormatted = number_format($productPriceRaw, 2); // Format for display
                            $productCategory = htmlspecialchars($row['category']);
                            $productImage = htmlspecialchars($row['image']);
                        ?>
                            <img src="../assets/products/<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" class="img-fluid rounded-top">
                            <div class="card-body">
                                <h5 class="product-name text-primary fw-bold"><?php echo $productName; ?></h5>
                                <p class="product-category text-muted"><strong>Category:</strong> <?php echo $productCategory; ?></p>
                                <p class="product-price text-success">
                                    <strong>Price:</strong> ₱<span id="base-price"><?php echo $productPriceRaw; ?></span>
                                </p>
                                <!-- Quantity Selector -->
                                <div class="quantity-selector mt-4">
                                    <label for="quantity" class="form-label fw-semibold">Quantity</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary decrease-quantity" type="button">-</button>
                                        <input type="number" id="quantity" class="form-control text-center" value="1" min="1">
                                        <button class="btn btn-outline-secondary increase-quantity" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                        <?php
                        } else {
                            echo "<p class='text-danger'>Product not found. Please go back and select a product.</p>";
                            exit();
                        }
                        ?>
                    </div>
                </div>

                <!-- Second Column: Customer Details -->
                <div class="col-lg-6">
                    <h4 class="section-title text-success">Customer Details</h4>
                    <div id="customer-details" class="card customer-details-card shadow-sm border-0">
                        <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control bg-light" value="Loading..." readonly>
                                </div>a
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control bg-light" value="Loading..." readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Shipping Address</label>
                                    <input type="text" class="form-control bg-light" value="Loading..." readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mode of Payment</label>
                                    <select id="payment-method" class="form-select bg-light">
                                        <option value="gcash">GCash</option>
                                        <option value="paymaya">Paymaya</option>
                                        <option value="paypal">PayPal</option>
                                    </select>
                                </div>
                                
                                <div class="action-buttons mt-4 d-flex gap-3">
                                    <button class="btn btn-success w-50 btn-add-to-cart" data-product-id="<?php echo $product_id; ?>">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                    <button class="btn btn-primary w-50 btn-checkout" data-product-id="<?php echo $product_id; ?>">
                                        <i class="bi bi-credit-card"></i> Check Out
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/profile-dropdown.js"></script>
    <script src="../assets/js/account-activation.js"></script>
</body>

</html>