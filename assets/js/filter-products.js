document.addEventListener("DOMContentLoaded", () => {
    const categoryButtons = document.querySelectorAll(".category-btn");
    const productItems = document.querySelectorAll(".product-item");
    const productsContainer = document.querySelector("#products-container");

    // Create a "No products found" message element
    const noProductsMessage = document.createElement("div");
    noProductsMessage.classList.add("no-products-message");
    noProductsMessage.textContent = "No products found in this category.";
    noProductsMessage.style.display = "none";
    productsContainer.appendChild(noProductsMessage);

    categoryButtons.forEach(button => {
        button.addEventListener("click", () => {
            // Remove 'active' class from all buttons
            categoryButtons.forEach(btn => btn.classList.remove("active"));
            // Add 'active' class to the clicked button
            button.classList.add("active");

            const category = button.getAttribute("data-category");
            let visibleItems = 0;

            productItems.forEach(item => {
                const card = item.querySelector(".card");
                // Show all products if 'all' is selected
                if (category === "all" || card.getAttribute("data-category") === category) {
                    item.classList.remove("hidden");
                    // Add a fade-in animation
                    item.classList.add("fade-in");
                    visibleItems++;
                } else {
                    item.classList.add("hidden");
                }
            });

            // Show/hide "No products found" message
            if (visibleItems === 0) {
                noProductsMessage.style.display = "block";
            } else {
                noProductsMessage.style.display = "none";
            }
        });
    });

    // Toggle sidebar on hamburger click
    const hamburger = document.querySelector(".hamburger");
    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");

    hamburger.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        content.classList.toggle("expanded");
    });
});