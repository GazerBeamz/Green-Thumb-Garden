function activateUser(userId, status) {
  if (status.toLowerCase() === "inactive") {
    // Directly activate the user via AJAX
    fetch("admin_dashboard.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=activate&user_id=${userId}`,
    })
      .then((response) => response.text())
      .then((data) => {
        alert("User activated successfully!");
        location.reload(); // Reload the page to reflect changes
      })
      .catch((error) => {
        console.error("Error activating user:", error);
        alert("Failed to activate user.");
      });
  } else {
    // Show the modal for other statuses
    showActionModal(userId, "activate");
  }
}
