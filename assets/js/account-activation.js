// Utility function to show a success alert using SweetAlert
function showSuccessAlert(title, text) {
  return Swal.fire({
      icon: "success",
      title: title,
      text: text,
      confirmButtonText: "OK",
  }).then(() => {
      location.reload(); // Reload the page to reflect changes
  });
}

// Utility function to show an error alert using SweetAlert
function showErrorAlert(title, text) {
  return Swal.fire({
      icon: "error",
      title: title,
      text: text,
      confirmButtonText: "OK",
  });
}

// Utility function to show a confirmation alert using SweetAlert
function showConfirmAlert(title, text) {
  return Swal.fire({
      title: title,
      text: text,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#10b981",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, activate!",
      cancelButtonText: "Cancel",
  });
}

// Function to show the action modal for deactivate/delete actions
function showActionModal(userId, action) {
  console.log("Show Action Modal:", userId, action); // Debugging
  document.getElementById("actionUserId").value = userId;
  document.getElementById("actionType").value = action;
  const modal = new bootstrap.Modal(document.getElementById("actionModal"));
  modal.show();
}

// Function to activate a user via AJAX
function performUserActivation(userId) {
  console.log("Performing user activation for ID:", userId); // Debugging

  fetch("manage_users.php", {
      method: "POST",
      headers: {
          "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=activate&user_id=${userId}`,
  })
      .then((response) => {
          console.log("Raw Response:", response); // Debugging
          return response.json();
      })
      .then((data) => {
          console.log("Response Data:", data); // Debugging
          if (data.status === "success") {
              showSuccessAlert(
                  "User Activated",
                  data.message || "The user has been successfully activated!"
              );
          } else {
              showErrorAlert(
                  "Activation Failed",
                  data.message || "An error occurred while activating the user."
              );
          }
      })
      .catch((error) => {
          console.error("Error activating user:", error);
          showErrorAlert(
              "Activation Failed",
              "An error occurred while activating the user."
          );
      });
}

// Function to handle the user activation process with confirmation
function activateUser(userId, status) {
  console.log("Activate User Function Called:", userId, status); // Debugging
  const normalizedStatus = status ? status.toLowerCase() : "unknown";

  if (normalizedStatus === "inactive") {
    showConfirmAlert("Are you sure?", "Do you want to activate this user?").then((result) => {
      if (result.isConfirmed) {
        performUserActivation(userId);
      }
    });
  } else {
    console.log("User is not inactive. Showing modal."); // Debugging
    showActionModal(userId, "activate");
  }
}