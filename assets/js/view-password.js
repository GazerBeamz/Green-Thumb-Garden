const togglePassword = document.querySelector("#togglePassword");
const passwordField = document.querySelector("#password");

const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
const confirmPasswordField = document.querySelector("#confirm_password");

togglePassword.addEventListener("click", function () {
    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);

    this.innerHTML =
        type === "password"
            ? `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
            </svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye-slash" viewBox="0 0 16 16">
                <path d="M13.359 11.238C12.226 12.347 10.743 13 8 13c-3.866 0-7-4-7-4s1.17-1.65 3.359-3.238"/>
                <path d="M3.646 3.646a.5.5 0 0 1 .708 0l10 10a.5.5 0 0 1-.708.708l-10-10a.5.5 0 0 1 0-.708z"/>
            </svg>`;
});

toggleConfirmPassword.addEventListener("click", function () {
    const type = confirmPasswordField.getAttribute("type") === "password" ? "text" : "password";
    confirmPasswordField.setAttribute("type", type);

    this.innerHTML =
        type === "password"
            ? `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
            </svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#28a745" class="bi bi-eye-slash" viewBox="0 0 16 16">
                <path d="M13.359 11.238C12.226 12.347 10.743 13 8 13c-3.866 0-7-4-7-4s1.17-1.65 3.359-3.238"/>
                <path d="M3.646 3.646a.5.5 0 0 1 .708 0l10 10a.5.5 0 0 1-.708.708l-10-10a.5.5 0 0 1 0-.708z"/>
            </svg>`;
});
