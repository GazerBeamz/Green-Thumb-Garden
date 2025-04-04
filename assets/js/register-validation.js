function handleFormSubmit(event) {
  event.preventDefault(); // Prevent the form from submitting immediately

  // Get the password and confirm password fields
  const passwordField = document.querySelector("#password");
  const confirmPasswordField = document.querySelector("#confirm_password");

  // Check if passwords match
  if (passwordField.value !== confirmPasswordField.value) {
    // Show error SweetAlert if passwords do not match
    Swal.fire({
      icon: "error",
      title: "Registration Failed",
      text: "Passwords do not match!",
      confirmButtonColor: "#28a745",
    });
    return false; // Stop form submission
  }

  // Show success SweetAlert if passwords match
  Swal.fire({
    icon: "success",
    title: "Registration Successful",
    text: "Welcome to Green Thumb Garden! Please check your email for details.",
    confirmButtonColor: "#28a745",
  }).then(() => {
    // Submit the form after the SweetAlert is closed
    event.target.submit();
  });

  return false; // Prevent default form submission
}
