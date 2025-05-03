// Sidebar toggle for mobile
const hamburger = document.querySelector(".hamburger");
const sidebar = document.querySelector(".sidebar");
// Cart functionality
document.addEventListener("DOMContentLoaded", () => {
    const cartItemsContainer = document.getElementById("cart-items");
    const cartTotalElement = document.getElementById("cart-total");
    const cartCountElement = document.querySelector(".cart-count");

    // Fetch cart items from the server
    const fetchCartItems = () => {
        fetch("../functions/PHP/fetch_add_cart.php")
            .then(response => response.json())
            .then(data => {
                console.log("Fetched cart items:", data); // Debugging log
                if (data.status === "success") {
                    populateCart(data.cart_items);
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error("Error fetching cart items:", error));
    };

    // Populate the cart with fetched items
    const populateCart = (cartItems) => {
        cartItemsContainer.innerHTML = ""; // Clear existing items
        let total = 0;
        let itemCount = 0;

        cartItems.forEach(item => {
            const cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");
            cartItem.setAttribute("data-id", item.cart_id);

            cartItem.innerHTML = `
                <img src="../assets/products/${item.image}" alt="${item.name}">
                <div class="cart-item-details">
                    <h6>${item.name}</h6>
                    <p>Category: Miscellaneous</p>
                </div>
                <div class="cart-item-price">₱${item.price}</div>
                <div class="cart-item-quantity">
                    <button class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                    <input type="number" class="form-control quantity-input" value="${item.quantity}" min="1" readonly>
                    <button class="btn btn-sm btn-outline-secondary increase-qty">+</button>
                </div>
                <div class="cart-item-total">₱${item.total}</div>
                <button class="btn btn-remove remove-item">Remove</button>
            `;

            cartItemsContainer.appendChild(cartItem);

            total += parseFloat(item.total);
            itemCount += parseInt(item.quantity);
        });

        cartTotalElement.textContent = total.toFixed(2);
        cartCountElement.textContent = itemCount;
    };

    // Update cart total and count
    const updateCart = () => {
        let total = 0;
        let itemCount = 0;
        const cartItems = cartItemsContainer.querySelectorAll(".cart-item");

        cartItems.forEach(item => {
            const price = parseFloat(item.querySelector(".cart-item-price").textContent.replace("₱", ""));
            const quantity = parseInt(item.querySelector(".quantity-input").value);
            const itemTotal = price * quantity;
            item.querySelector(".cart-item-total").textContent = `₱${itemTotal.toFixed(2)}`;
            total += itemTotal;
            itemCount += quantity;
        });

        cartTotalElement.textContent = total.toFixed(2);
        cartCountElement.textContent = itemCount;
    };

    // Increase quantity
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("increase-qty")) {
            const quantityInput = e.target.parentElement.querySelector(".quantity-input");
            let quantity = parseInt(quantityInput.value);
            quantityInput.value = quantity + 1;
            updateCart();
        }
    });

    // Decrease quantity
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("decrease-qty")) {
            const quantityInput = e.target.parentElement.querySelector(".quantity-input");
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
                updateCart();
            }
        }
    });

    // Remove item
    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-item")) {
            e.target.closest(".cart-item").remove();
            updateCart();
        }
    });

    // Initialize cart
    fetchCartItems();
});