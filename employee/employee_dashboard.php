<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: ../login.php");
    exit();
}

// Fetch admin ID dynamically
$adminResult = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$admin = mysqli_fetch_assoc($adminResult);
$adminId = $admin['id'];

// Handle AJAX message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['ajax'])) {
    $recipientId = intval($_POST['recipient_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $senderId = $_SESSION['user_id'];

    $query = "INSERT INTO messages (sender_id, recipient_id, message, is_read) VALUES ($senderId, $recipientId, '$message', 0)";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => htmlspecialchars($message), 'isEmployee' => true]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit();
}

// Handle AJAX product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product' && isset($_POST['ajax'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_category = mysqli_real_escape_string($conn, $_POST['product_category']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $employee_id = $_SESSION['user_id'];
    $product_image = '';

    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "../assets/products/";
        $fileName = basename($_FILES['product_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath)) {
                $product_image = $fileName;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload product image.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG, JPEG, PNG, and GIF files are allowed.']);
            exit();
        }
    }

    $query = "INSERT INTO products (name, description, category, price, image, employee_id) 
              VALUES ('$product_name', '$product_description', '$product_category', '$product_price', '$product_image', '$employee_id')";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add product: ' . mysqli_error($conn)]);
    }
    exit();
}

// Handle AJAX product editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_product' && isset($_POST['ajax'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_category = mysqli_real_escape_string($conn, $_POST['product_category']);

    $query = "UPDATE products SET name = '$product_name', price = '$product_price', category = '$product_category' WHERE id = $product_id";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update product: ' . mysqli_error($conn)]);
    }
    exit();
}

// Handle AJAX product removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_product' && isset($_POST['ajax'])) {
    $product_id = intval($_POST['id']);
    $query = "DELETE FROM products WHERE id = '$product_id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Product removed successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove product: ' . mysqli_error($conn)]);
    }
    exit();
}

// Fetch employee details
$employee_id = $_SESSION['user_id'];
$employee = mysqli_fetch_assoc(mysqli_query($conn, "SELECT firstname, lastname, email, profile_image FROM users WHERE id = '$employee_id'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Green Thumb Garden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/employee_dashboard.css" rel="stylesheet">
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container text-center py-4">
            <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
            <h4 class="logo-text mt-2">Employee Dashboard</h4>
        </div>
        <nav class="nav flex-column">
            <a href="employee_dashboard.php" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="#manage-products" class="nav-link"><i class="fas fa-box"></i> Manage Products</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="content">
        <header class="d-flex justify-content-between align-items-center py-3 px-4 shadow-sm">
            <h1 class="text-primary">Welcome, <?php echo htmlspecialchars($employee['firstname']); ?></h1>
            <div class="profile-container">
                <div class="profile-icon">
                    <img src="../assets/profiles/<?php echo htmlspecialchars($employee['profile_image'] ?: 'profile-placeholder.png'); ?>" alt="Employee Profile" class="profile-img">
                </div>
                <div class="profile-hover d-none">
                    <p><?php echo htmlspecialchars($employee['firstname'] . ' ' . $employee['lastname']); ?></p>
                    <a href="../profile.php"><i class="bi bi-pencil-square"></i> My Profile</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </header>

        <!-- Dashboard Overview -->
        <section id="dashboard" class="py-4 px-4">
            <h4 class="section-title">Dashboard Overview</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-4">
                        <h6>Total Products</h6>
                        <p class="display-6 text-primary">
                            <?php
                            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Manage Products -->
        <section id="manage-products" class="py-4 px-4">
            <h4 class="section-title">Manage Products</h4>
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM products");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>\${$row['price']}</td>
                                <td>" . htmlspecialchars($row['category']) . "</td>
                                <td>
                                    <a href='#' class='btn btn-warning btn-sm edit-product-btn' data-bs-toggle='modal' data-bs-target='#editProductModal' 
                                       data-id='{$row['id']}' 
                                       data-name='" . htmlspecialchars($row['name']) . "' 
                                       data-price='{$row['price']}' 
                                       data-category='" . htmlspecialchars($row['category']) . "'>
                                       Edit
                                    </a>
                                    <a href='#' class='btn btn-danger btn-sm remove-product-btn' data-id='{$row['id']}'>Remove</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Chat Head -->
        <div id="chat-head" class="chat-head">
            <button id="chat-toggle" class="btn btn-primary position-relative">
                <i class="fas fa-comments"></i>
                <span id="chat-notification" class="chat-notification"></span>
            </button>
            <div id="chat-box" class="chat-box d-none">
                <div class="chat-header">
                    <h6>Chat with Admin</h6>
                    <button id="chat-close" class="btn btn-sm btn-danger">×</button>
                </div>
                <div id="chat-messages" class="chat-messages"></div>
                <form id="chat-form" method="POST">
                    <input type="hidden" name="recipient_id" value="<?php echo $adminId; ?>">
                    <input type="hidden" name="ajax" value="1">
                    <div class="input-group">
                        <input type="text" name="message" id="chat-input" class="form-control" placeholder="Type a message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_product">
                        <input type="hidden" name="ajax" value="1">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_price" class="form-label">Product Price</label>
                            <input type="number" class="form-control" id="product_price" name="product_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_description" class="form-label">Product Description</label>
                            <textarea class="form-control" id="product_description" name="product_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="product_category" class="form-label">Product Category</label>
                            <select class="form-select" id="product_category" name="product_category" required>
                                <option value="Flowers">Flowers</option>
                                <option value="Vegetable">Vegetable</option>
                                <option value="Herb">Herb</option>
                                <option value="Miscellaneous">Miscellaneous</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" name="action" value="edit_product">
                        <input type="hidden" id="edit_product_id" name="product_id">
                        <input type="hidden" name="ajax" value="1">
                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_price" class="form-label">Product Price</label>
                            <input type="number" class="form-control" id="edit_product_price" name="product_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_product_category" class="form-label">Product Category</label>
                            <select class="form-select" id="edit_product_category" name="product_category" required>
                                <option value="Flowers">Flowers</option>
                                <option value="Vegetable">Vegetable</option>
                                <option value="Herb">Herb</option>
                                <option value="Miscellaneous">Miscellaneous</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/profile-dropdown.js"></script>
    <script src="../assets/js/chat.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
            const adminId = <?php echo json_encode($adminId); ?>;

            // Edit Product Modal
            const editProductModal = document.getElementById("editProductModal");
            editProductModal.addEventListener("show.bs.modal", (event) => {
                const button = event.relatedTarget;
                const productId = button.getAttribute("data-id");
                const productName = button.getAttribute("data-name");
                const productPrice = button.getAttribute("data-price");
                const productCategory = button.getAttribute("data-category");

                document.getElementById("edit_product_id").value = productId;
                document.getElementById("edit_product_name").value = productName;
                document.getElementById("edit_product_price").value = productPrice;
                document.getElementById("edit_product_category").value = productCategory;
            });

            // Handle Add Product Form Submission
            document.getElementById('addProductForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);

                fetch('employee_dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred.'
                    });
                });
            });

            // Handle Edit Product Form Submission
            document.getElementById('editProductForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);

                fetch('employee_dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred.'
                    });
                });
            });

            // Handle Remove Product with Confirmation
            document.querySelectorAll('.remove-product-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productId = button.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Delete'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('action', 'remove_product');
                            formData.append('id', productId);
                            formData.append('ajax', '1');

                            fetch('employee_dashboard.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted',
                                        text: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An unexpected error occurred.'
                                });
                            });
                        }
                    });
                });
            });

            // Check the session
            setInterval(() => {
                fetch('../check_session.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.session_status === 'inactive') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Session Ended',
                                text: 'Your session has ended. Please log in again.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.href = '../login.php?message=Session Ended';
                            });
                        }
                    })
                    .catch(error => console.error('Error checking session:', error));
            }, 5000);
        });
    </script>
</body>
</html>