document.addEventListener("DOMContentLoaded", () => {
    const categoryButtons = document.querySelectorAll(".category-btn");
    const productItems = document.querySelectorAll(".product-item");

    categoryButtons.forEach(button => {
        button.addEventListener("click", () => {
            // Remove 'active' class from all buttons
            categoryButtons.forEach(btn => btn.classList.remove("active"));
            // Add 'active' class to the clicked button
            button.classList.add("active");

            const category = button.getAttribute("data-category");

            productItems.forEach(item => {
                const card = item.querySelector(".card");
                // Show all products if 'all' is selected
                if (category === "all" || card.getAttribute("data-category") === category) {
                    item.classList.remove("hidden");
                } else {
                    item.classList.add("hidden");
                }
            });
        });
    });
});