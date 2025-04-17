document.addEventListener("DOMContentLoaded", () => {
    const profileContainers = document.querySelectorAll(".profile-container");

    profileContainers.forEach((container) => {
        const profileHover = container.querySelector(".profile-hover");

        container.addEventListener("click", (event) => {
            event.stopPropagation(); // Prevent click from propagating to the document
            profileHover.classList.toggle("d-none");
        });
    });

    // Close the dropdown when clicking outside
    document.addEventListener("click", () => {
        document.querySelectorAll(".profile-hover").forEach((dropdown) => {
            dropdown.classList.add("d-none");
        });
    });
});