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
    <title>Customer Dashboard - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="hamburger"><i class="bi bi-list"></i></button>
                <h1 class="animate__animated animate__fadeIn">Explore Your Gardening Journey</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="search-bar">
                    <input type="text" class="form-control" placeholder="Search products...">
                    <button class="btn btn-success"><i class="bi bi-search"></i></button>
                </div>
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

        <!-- Recommendations -->
        <section class="recommendations mb-5">
            <h4 class="animate__animated animate__fadeIn">Recommendations</h4>
            <div class="d-flex gap-4">
                <div class="card animate__animated animate__zoomIn">
                    <img src="../assets/products/product.jpg" alt="Product 1" class="img-fluid">
                    <h6>Product 1</h6>
                    <p>₱600.00</p>
                </div>
                <div class="card animate__animated animate__zoomIn" style="animation-delay: 0.2s;">
                    <img src="../assets/products/product1.jpg" alt="Product 2" class="img-fluid">
                    <h6>Product 2</h6>
                    <p>₱750.00</p>
                </div>
                <div class="card animate__animated animate__zoomIn" style="animation-delay: 0.4s;">
                    <img src="../assets/products/product2.jpg" alt="Product 3" class="img-fluid">
                    <h6>Product 3</h6>
                    <p>₱500.00</p>
                </div>
                <div class="card animate__animated animate__zoomIn" style="animation-delay: 0.6s;">
                    <img src="../assets/products/product3.jpg" alt="Product 4" class="img-fluid">
                    <h6>Product 4</h6>
                    <p>₱900.00</p>
                </div>
                <div class="card animate__animated animate__zoomIn" style="animation-delay: 0.8s;">
                    <img src="../assets/products/product4.jpg" alt="Product 5" class="img-fluid">
                    <h6>Product 5</h6>
                    <p>₱1,000.00</p>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="categories mb-5">
            <h4 class="animate__animated animate__fadeIn">Categories</h4>
            <div class="d-flex gap-3 flex-wrap">
                <button class="btn btn-outline-success category-btn active" data-category="all">All Plants</button>
                <button class="btn btn-outline-success category-btn" data-category="flower">Flowers</button>
                <button class="btn btn-outline-success category-btn" data-category="vegetable">Vegetable</button>
                <button class="btn btn-outline-success category-btn" data-category="herbs">Herb</button>
                <button class="btn btn-outline-success category-btn" data-category="product">Miscellaneous</button>
            </div>
        </section>

        <!-- Products Section -->
        <section class="products mb-5">
            <h4 class="animate__animated animate__fadeIn">Products</h4>
            <div class="grid-container" id="products-container">
                <?php
                // Fetch products from the database
                $query = "SELECT * FROM products";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $productId = $row['id'];
                        $productName = htmlspecialchars($row['name']);
                        $productPrice = number_format($row['price'], 2);
                        $productCategory = strtolower(htmlspecialchars($row['category']));
                        $productImage = htmlspecialchars($row['image']);
                ?>
                        <!-- Wrap the product card in a link -->
                        <a href="check_out.php?product_id=<?php echo $productId; ?>" class="product-item-link">
                            <div class="product-item" data-category="<?php echo $productCategory; ?>">
                                <div class="card">
                                    <img src="../assets/products/<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" class="img-fluid">
                                    <h6><?php echo $productName; ?></h6>
                                    <p>₱<?php echo $productPrice; ?></p>
                                </div>
                            </div>
                        </a>
                <?php
                    }
                } else {
                    echo "<p class='no-products-message'>No products available.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="../assets/js/filter-products.js"></script> -->
    <script src="../assets/js/profile-dropdown.js"></script>
    <script src="../functions/JS/cartcounter.js"></script>
    <script src="../assets/js/account-activation.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const categoryButtons = document.querySelectorAll(".category-btn");
        const productItems = document.querySelectorAll(".product-item");

        categoryButtons.forEach(button => {
            button.addEventListener("click", () => {
                const category = button.getAttribute("data-category");

                // Highlight the active button
                categoryButtons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");

                // Filter products
                productItems.forEach(item => {
                    if (category === "all" || item.getAttribute("data-category") === category) {
                        item.style.display = "block";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        });
    });
    // check the session
    setInterval(() => {
        fetch('../check_session.php')
            .then(response => response.json())
            .then(data => {
                if (data.session_status === 'inactive') {
                    alert('Session Ended');
                    window.location.href = '../login.php?message=Session Ended';
                }
            })
            .catch(error => console.error('Error checking session:', error));
    }, 5000); // Check every 5 seconds
</script>


</html>