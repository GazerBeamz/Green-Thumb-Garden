document.addEventListener("DOMContentLoaded", () => {
    const cartItemsContainer = document.getElementById("cart-items");
    const emptyCartMessage = document.getElementById("empty-cart-message");
    const cartSummary = document.querySelector(".cart-summary");
    const cartTotalElement = document.getElementById("cart-total");
    const cartCountElement = document.querySelector(".cart-count");

    // Fetch cart items from the server
    const fetchCartItems = () => {
        fetch("../functions/PHP/fetch_add_cart.php")
            .then((response) => response.json())
            .then((data) => {
                if (data.status === "success") {
                    if (data.cart_items.length > 0) {
                        populateCart(data.cart_items);
                        emptyCartMessage.style.display = "none";
                        cartSummary.style.display = "block";
                    } else {
                        cartItemsContainer.innerHTML = "";
                        emptyCartMessage.style.display = "block";
                        cartSummary.style.display = "none";
                    }
                } else {
                    console.error(data.message);
                }
            })
            .catch((error) => console.error("Error fetching cart items:", error));
    };

    // Populate the cart with fetched items
    const populateCart = (cartItems) => {
        cartItemsContainer.innerHTML = ""; // Clear existing items
        let total = 0;
        let itemCount = 0;

        cartItems.forEach((item) => {
            const cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");
            cartItem.setAttribute("data-id", item.cart_id);

            cartItem.innerHTML = `
                <img src="../assets/products/${item.image}" alt="${item.name}">
                <div class="cart-item-details">
                    <h6>${item.name}</h6>
                    <p>Category: ${item.product_category}</p>
                </div>
                <div class="cart-item-price">₱${item.price}</div>
                <div class="cart-item-quantity">
                    <button class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                    <input type="number" class="form-control quantity-input" value="${item.quantity}" min="1" readonly>
                    <button class="btn btn-sm btn-outline-secondary increase-qty">+</button>
                </div>
                <button class="btn btn-remove remove-item">Remove</button>
            `;

            cartItemsContainer.appendChild(cartItem);

            total += parseFloat(item.total);
            itemCount += parseInt(item.quantity);
        });

        cartTotalElement.textContent = total.toFixed(2);
        cartCountElement.textContent = itemCount;
    };

    // Update cart total
    const updateCartTotal = () => {
        let total = 0;
        const cartItems = cartItemsContainer.querySelectorAll(".cart-item");

        cartItems.forEach((item) => {
            const price = parseFloat(item.querySelector(".cart-item-price").textContent.replace("₱", ""));
            const quantity = parseInt(item.querySelector(".quantity-input").value);
            total += price * quantity;
        });

        cartTotalElement.textContent = total.toFixed(2);
    };

    // Increase quantity
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("increase-qty")) {
            const quantityInput = e.target.parentElement.querySelector(".quantity-input");
            let quantity = parseInt(quantityInput.value);
            quantityInput.value = quantity + 1;
            updateCartTotal();
        }
    });

    // Decrease quantity
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("decrease-qty")) {
            const quantityInput = e.target.parentElement.querySelector(".quantity-input");
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                updateCartTotal();
            }
        }
    });

    // Remove item
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            const cartItem = e.target.closest(".cart-item");
            const cartId = cartItem.getAttribute("data-id");

            fetch("../functions/PHP/remove_cart_item.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ cart_id: cartId }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        cartItem.remove();
                        updateCartTotal();
                    } else {
                        console.error(data.message);
                    }
                })
                .catch((error) => console.error("Error removing item:", error));
        }
    });

    // Initialize cart
    fetchCartItems();
});