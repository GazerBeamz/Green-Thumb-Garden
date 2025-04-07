document.addEventListener("DOMContentLoaded", () => {
    const otpInputs = document.querySelectorAll(".otp-input");

    otpInputs.forEach((input, index) => {
        // Handle input event
        input.addEventListener("input", (e) => {
            const value = e.target.value;

            // Move to the next input if a single character is entered
            if (value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        // Handle paste event
        input.addEventListener("paste", (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData("text").trim();
            const pastedValues = pasteData.split("");

            otpInputs.forEach((otpInput, i) => {
                otpInput.value = pastedValues[i] || ""; // Fill inputs with pasted values
            });

            // Focus the last filled input
            otpInputs[Math.min(pastedValues.length, otpInputs.length) - 1].focus();
        });

        // Handle backspace event
        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && input.value === "" && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    // Combine all OTP inputs into a single hidden input before form submission
    const otpForm = document.querySelector("form[onsubmit='return combineOtpInputs()']");
    if (otpForm) {
        otpForm.addEventListener("submit", () => {
            const otpHiddenInput = document.getElementById("otp");
            let otpValue = "";

            otpInputs.forEach((input) => {
                otpValue += input.value;
            });

            otpHiddenInput.value = otpValue;

            // Ensure all fields are filled
            if (otpValue.length < otpInputs.length) {
                alert("Please fill in all OTP fields.");
                return false;
            }

            return true; // Allow form submission
        });
    }
});

