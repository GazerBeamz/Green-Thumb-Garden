<!-- index.php -->
<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
            <img src="assets/images/greenthumb_garden.png" alt="Green Thumb Logo" class="logo-img">
            Green Thumb
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav gap-4">
                <li class="nav-item">
                    <a class="nav-link active fw-medium" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="#products">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="#">Contact</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'customer') { ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="customer/cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary px-4 py-1 fw-medium" href="logout.php">Logout</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary px-4 py-1 fw-medium" href="login.php">Login</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section position-relative text-white overflow-hidden">
    <div class="hero-bg-overlay"></div>
    <div class="container position-relative z-1 py-6">
        <div class="row align-items-center py-5">
            <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                <h1 class="display-2 fw-bold mb-4 animate__animated animate__fadeInDown">
                    Green Thumb Garden
                </h1>
                <p class="lead mb-5 animate__animated animate__fadeInUp animate__delay-1s">
                    Elevate Your Outdoor Living with Premium Gardening Essentials
                </p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="#products" class="btn btn-primary px-5 py-3 fw-semibold shadow-sm">Shop Now</a>
                    <a href="#products" class="btn btn-outline-light px-5 py-3 fw-semibold shadow-sm">Explore</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="assets/images/hero-garden.png" alt="Garden Hero" class="img-fluid hero-image animate__animated animate__zoomIn">
            </div>
        </div>
    </div>
    <div class="hero-wave position-absolute bottom-0 w-100"></div>
</section>

<!-- Products Section -->
<section class="products-section py-6 bg-light">
    <div class="container py-5">
        <div class="text-center mb-6">
            <h2 id="products" class="section-title fw-bold mb-3 animate__animated animate__fadeIn">
                Our Gardening Collection
            </h2>
            <p class="text-muted lead animate__animated animate__fadeIn animate__delay-1s">
                Premium tools and plants for every green enthusiast
            </p>
        </div>
        <div class="row g-4">
            <?php while ($product = mysqli_fetch_assoc($result)) { ?>
                <div class="col-md-6 col-lg-4">
                    <div class="product-card h-100 position-relative overflow-hidden shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="assets/images/<?php echo $product['image']; ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-overlay d-flex align-items-center justify-content-center">
                                <a href="product.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary btn-sm px-4 py-2 fw-medium">View Details</a>
                            </div>
                        </div>
                        <div class="card-body p-4 text-center">
                            <h5 class="card-title mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small mb-3">
                                <?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...
                            </p>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <span class="price fw-bold text-success">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </span>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'customer') { ?>
                                    <a href="customer/cart.php?action=add&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-outline-success btn-sm px-3 py-1 fw-medium">Add to Cart</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>