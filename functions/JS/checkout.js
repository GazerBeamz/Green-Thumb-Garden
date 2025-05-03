// Sidebar toggle for mobile
const hamburger = document.querySelector(".hamburger");
const sidebar = document.querySelector(".sidebar");
hamburger.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});

// Fetch customer details and handle quantity/add-to-cart/checkout logic
document.addEventListener("DOMContentLoaded", () => {
  const quantityInput = document.getElementById("quantity");
  const basePrice = parseFloat(document.getElementById("product-price").value);
  const productId = parseInt(document.getElementById("product-id").value);
  const totalPriceElement = document.getElementById("total-price");
  const MAX_QUANTITY = 10; // Hardcoded maximum quantity

  // Update total price based on quantity
  const updateTotalPrice = () => {
    const quantity = parseInt(quantityInput.value) || 1; // Default to 1 if NaN
    const updatedPrice = (basePrice * quantity).toFixed(2);
    totalPriceElement.textContent = updatedPrice;
    console.log(`Updated total price to: ₱${updatedPrice}`);
  };

  // Setup quantity buttons
  const setupQuantityButtons = () => {
    const increaseButton = document.getElementById("increase-quantity");
    const decreaseButton = document.getElementById("decrease-quantity");

    if (
      increaseButton &&
      decreaseButton &&
      quantityInput &&
      totalPriceElement
    ) {
      // Increase quantity
      increaseButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value) || 1;
        console.log(
          `Increase button clicked. Current value: ${currentValue}, Max value: ${MAX_QUANTITY}`
        );

        currentValue += 1; // Increment first
        if (currentValue <= MAX_QUANTITY) {
          quantityInput.value = currentValue;
        } else {
          quantityInput.value = MAX_QUANTITY; // Cap at 30
          Swal.fire({
            icon: "warning",
            title: "Stock Limit",
            text: `Maximum stock limit reached ${MAX_QUANTITY}`,
            showConfirmButton: false,
            timer: 1500,
          });
        }
        updateTotalPrice();
      });

      // Decrease quantity
      decreaseButton.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value) || 1;
        console.log(`Decrease button clicked. Current value: ${currentValue}`);

        currentValue -= 1; // Decrement first
        if (currentValue >= 1) {
          quantityInput.value = currentValue;
        } else {
          quantityInput.value = 1; // Ensure it doesn't go below 1
        }
        updateTotalPrice();
      });
    } else {
      console.error("Required elements for quantity buttons not found!");
    }
  };

  // Attach button listeners for add-to-cart and checkout
  const attachButtonListeners = () => {
    const addToCartButton = document.querySelector(".btn-add-to-cart");
    const checkoutButton = document.querySelector(".btn-checkout");

    if (addToCartButton) {
      addToCartButton.addEventListener("click", () => {
        const quantity = parseInt(quantityInput.value);
        if (isNaN(quantity) || quantity < 1 || quantity > MAX_QUANTITY) {
          Swal.fire({
            icon: "error",
            title: "Invalid Quantity",
            text: `Please select a valid quantity (1-${MAX_QUANTITY}).`,
            showConfirmButton: false,
            timer: 1500,
          });
          return;
        }

        // Prepare data
        const productId = document.getElementById("product-id").value;
        const productName = document.querySelector(".product-name").textContent;
        const productDescription = document.querySelector(".order-details .product-description").textContent;
        const productCategory = document.getElementById("product-category").value; // Now defined in HTML
        const productPrice = document.getElementById("product-price").value;

        // Validate all fields
        if (!productId || !productName || !productCategory || !productPrice) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Missing product details.",
            showConfirmButton: false,
            timer: 1500,
          });
          return;
        }

        // AJAX Request
        fetch("../functions/PHP/insert_cart.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            product_description: productDescription,
            product_category: productCategory,
            product_price: productPrice,
            quantity: quantity,
          }),
        })
          .then((res) => {
            if (!res.ok) {
              throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.text(); // Get raw response first
          })
          .then((text) => {
            try {
              const data = JSON.parse(text);
              console.log("Response from server:", data); // Debugging log
              if (data.success) {
                Swal.fire({
                  icon: "success",
                  title: "Added to Cart",
                  text: `Added ${quantity} item(s) to cart.`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: data.message || "Something went wrong.",
                });
              }
            } catch (e) {
              console.error("Invalid JSON response:", text);
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Invalid response from server. Check console for details.",
              });
            }
          })
          .catch((error) => {
            console.error("Error adding to cart:", error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Failed to add product to cart.",
            });
          });
      });
    }

    if (checkoutButton) {
      checkoutButton.addEventListener("click", () => {
        const paymentMethod = document.getElementById("payment-method").value;
        if (!paymentMethod) {
          Swal.fire({
            icon: "error",
            title: "Payment Method Required",
            text: "Please select a payment method.",
            showConfirmButton: false,
            timer: 1500,
          });
          return;
        }
        Swal.fire({
          icon: "success",
          title: "Checkout Initiated",
          text: `Proceeding to checkout with ${paymentMethod}...`,
          showConfirmButton: false,
          timer: 1500,
        });
      });
    }
  };

  // Fetch customer details and update input fields
  fetch("../functions/PHP/fetch_customer_details.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("fullname-input").value =
          data.fullname || "Not provided";
        document.getElementById("contact-input").value =
          data.contact || "Not provided";
        document.getElementById("address-input").value =
          data.address || "Not provided";
        console.log("Customer details updated successfully.");
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: data.message,
          showConfirmButton: false,
          timer: 2000,
        });
      }
    })
    .catch((error) => {
      console.error("Error fetching customer details:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Failed to load customer details. Using default values.",
        showConfirmButton: false,
        timer: 2000,
      });
    });

  // Session check
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

  // Initialize
  setupQuantityButtons();
  attachButtonListeners();
  updateTotalPrice(); // Initialize total price on load
});