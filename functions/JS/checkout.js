// Sidebar toggle for mobile
const hamburger = document.querySelector('.hamburger');
const sidebar = document.querySelector('.sidebar');
hamburger.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});

// Fetch customer details and other logic
document.addEventListener("DOMContentLoaded", () => {
    const customerDetailsContainer = document.getElementById("customer-details");

    // Get the base price from the hidden input
    const basePrice = parseFloat(document.getElementById("product-price").value);
    const quantityInput = document.getElementById("quantity");

    let totalPriceElement; // Declare here, will be assigned after fetch

    const updateTotalPrice = () => {
        const quantity = parseInt(quantityInput.value);
        const updatedPrice = (basePrice * quantity).toFixed(2);
        if (totalPriceElement) {
            totalPriceElement.textContent = updatedPrice; // Update only the total price
        }
    };

    // Fetch customer details
    fetch("../functions/PHP/fetch_customer_details.php")
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.status === "success") {
                const initialPrice = (basePrice * parseInt(quantityInput.value)).toFixed(2);
                customerDetailsContainer.innerHTML = `
                  <div class="card-body">
                    <div class="mb-3 text-start d-flex align-items-center">
                      <label class="form-label me-3" style="width: 150px;">Full Name</label>
                      <input type="text" class="form-control" value="${data.fullname}" readonly>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label class="form-label me-3" style="width: 150px;">Contact Number</label>
                      <input type="text" class="form-control" value="${data.contact}" readonly>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label class="form-label me-3" style="width: 150px;">Shipping Address</label>
                      <input type="text" class="form-control" value="${data.address}" readonly>
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
                      <p class="total-text">Total: <span class="total-amount">₱<span id="total-price">${initialPrice}</span></span></p>
                    </div>
                    <div class="action-buttons mt-4 d-flex gap-3">
                      <button class="btn btn-add-to-cart w-50" data-product-id="${data.product_id}">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                      </button>
                      <button class="btn btn-checkout w-50" data-product-id="${data.product_id}">
                        <i class="bi bi-credit-card"></i> Checkout
                      </button>
                    </div>
                  </div>
                `;
                // Assign totalPriceElement after the DOM is updated
                totalPriceElement = document.getElementById("total-price");
                attachButtonListeners();
                updateTotalPrice(); // Ensure the price is updated after DOM changes
            } else {
                customerDetailsContainer.innerHTML = `<p>${data.message}</p>`;
            }
        })
        .catch((error) => {
            console.error("Error fetching customer details:", error);
            customerDetailsContainer.innerHTML = `<p>An error occurred while fetching customer details.</p>`;
        });

    function attachButtonListeners() {
        const addToCartButton = document.querySelector(".btn-add-to-cart");
        const checkoutButton = document.querySelector(".btn-checkout");

        if (addToCartButton) {
            addToCartButton.addEventListener("click", (e) => {
                const productId = e.target.getAttribute("data-product-id");
                Swal.fire({
                    icon: "success",
                    title: "Added to Cart",
                    text: "The product has been added to your cart!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            });
        }

        if (checkoutButton) {
            checkoutButton.addEventListener("click", (e) => {
                const productId = e.target.getAttribute("data-product-id");
                const paymentMethod = document.getElementById("payment-method").value;

                Swal.fire({
                    icon: "success",
                    title: "Checkout Initiated",
                    text: `Proceeding to checkout with ${paymentMethod}...`,
                    showConfirmButton: false,
                    timer: 1500,
                });
            });
        }
    }

    setInterval(() => {
        fetch("../check_session.php")
            .then((response) => response.json())
            .then((data) => {
                if (data.session_status === "inactive") {
                    Swal.fire({
                        icon: "warning",
                        title: "Session Ended",
                        text: "Your session has ended. Please log in again.",
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        window.location.href = "../login.php?message=Session Ended";
                    });
                }
            })
            .catch((error) => console.error("Error checking session:", error));
    }, 5000);

    const increaseButton = document.getElementById("increase-quantity");
    const decreaseButton = document.getElementById("decrease-quantity");

    increaseButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
        updateTotalPrice();
    });

    decreaseButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotalPrice();
        }
    });

    quantityInput.addEventListener("input", () => {
        let currentValue = parseInt(quantityInput.value);
        if (isNaN(currentValue) || currentValue < 1) {
            quantityInput.value = 1;
        }
        updateTotalPrice();
    });

    // Initial update for the total price in the Customer Details section
    updateTotalPrice();
});