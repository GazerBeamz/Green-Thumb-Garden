document.addEventListener("DOMContentLoaded", () => {
    const cartCountElement = document.querySelector(".cart-count");

    const updateCartCount = () => {
        fetch("../functions/PHP/fetch_cart_count.php")
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    cartCountElement.textContent = data.cart_count;
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error("Error fetching cart count:", error));
    };

    // Update the cart count on page load
    updateCartCount();

    // Optionally, you can set an interval to refresh the cart count periodically
    setInterval(updateCartCount, 3000); // Refresh every 5 seconds
});