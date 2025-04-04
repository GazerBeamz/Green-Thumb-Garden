function handleFormSubmit(event) {
  event.preventDefault(); // Prevent the form from submitting immediately

  // Show SweetAlert
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
