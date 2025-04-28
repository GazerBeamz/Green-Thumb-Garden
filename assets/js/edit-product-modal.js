document.addEventListener("DOMContentLoaded", () => {
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
});