document.addEventListener("DOMContentLoaded", () => {
  const customerDetailsContainer = document.getElementById("customer-details");

  // Fetch customer details
  fetch("functions/fetch_customer_details.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        customerDetailsContainer.innerHTML = `
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="${data.fullname}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" value="${data.contact}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shipping Address</label>
                            <input type="text" class="form-control" value="${data.address}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mode of Payment</label>
                            <select id="payment-method" class="form-select bg-light">
                                <option value="gcash">GCash</option>
                                <option value="paymaya">Paymaya</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn btn-add-to-cart" data-product-id="${data.product_id}">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                            <button class="btn btn-checkout" data-product-id="${data.product_id}">
                                <i class="bi bi-credit-card"></i> Check Out
                            </button>
                        </div>
                    </div>
                `;

        // Reattach event listeners after updating the DOM
        attachButtonListeners();
      } else {
        customerDetailsContainer.innerHTML = `<p>${data.message}</p>`;
      }
    })
    .catch((error) => {
      console.error("Error fetching customer details:", error);
      customerDetailsContainer.innerHTML = `<p>An error occurred while fetching customer details.</p>`;
    });

  // Function to attach event listeners to buttons
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
        }).then(() => {
          window.location.href = `checkout_confirmation.php?product_id=${productId}&payment_method=${paymentMethod}`;
        });
      });
    }
  }

  // Check the session periodically
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

  const quantityInput = document.getElementById("quantity");
  const decreaseButton = document.querySelector(".decrease-quantity");
  const increaseButton = document.querySelector(".increase-quantity");
  const productPriceElement = document.getElementById("base-price");
  const displayPriceElement = document.querySelector(".product-price");
  const basePrice = parseFloat(productPriceElement.textContent);

  // Function to update the price based on quantity
  const updatePrice = () => {
    const quantity = parseInt(quantityInput.value);
    const updatedPrice = (basePrice * quantity).toFixed(2);
    displayPriceElement.innerHTML = `<strong>Price:</strong> ₱${updatedPrice}`;
  };

  // Decrease quantity
  decreaseButton.addEventListener("click", () => {
    let currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
      quantityInput.value = currentValue - 1;
      updatePrice();
    }
  });

  // Increase quantity
  increaseButton.addEventListener("click", () => {
    let currentValue = parseInt(quantityInput.value);
    quantityInput.value = currentValue + 1;
    updatePrice();
  });

  // Update price when quantity is manually changed
  quantityInput.addEventListener("input", () => {
    let currentValue = parseInt(quantityInput.value);
    if (isNaN(currentValue) || currentValue < 1) {
      quantityInput.value = 1;
    }
    updatePrice();
  });
});
