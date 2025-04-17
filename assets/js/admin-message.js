document.addEventListener("DOMContentLoaded", () => {
  const chatToggle = document.getElementById("chat-toggle");
  const chatList = document.getElementById("chat-list");
  const chatBox = document.getElementById("chat-box");
  const chatClose = document.getElementById("chat-close");
  const chatCloseBox = document.getElementById("chat-close-box");
  const chatBack = document.getElementById("chat-back");
  const chatEmployees = document.getElementById("chat-employees");
  const chatMessages = document.getElementById("chat-messages");
  const chatForm = document.getElementById("chat-form");
  const chatNotification = document.getElementById("chat-notification");
  let currentRecipientId = null;

  // Toggle chat list visibility
  chatToggle.addEventListener("click", () => {
    chatList.classList.toggle("d-none");
    chatBox.classList.add("d-none");
    if (!chatList.classList.contains("d-none")) {
      chatNotification.classList.remove("active");
      markMessagesAsRead();
      loadEmployeeChats();
    }
  });

  chatClose.addEventListener("click", () => {
    chatList.classList.add("d-none");
  });

  chatCloseBox.addEventListener("click", () => {
    chatBox.classList.add("d-none");
    chatList.classList.remove("d-none");
  });

  chatBack.addEventListener("click", () => {
    chatBox.classList.add("d-none");
    chatList.classList.remove("d-none");
  });

  // Load employee chats
  function loadEmployeeChats() {
    fetch("../fetch_employee_chats.php")
      .then((response) => response.json())
      .then((data) => {
        chatEmployees.innerHTML = "";
        data.forEach((employee) => {
          const employeeDiv = document.createElement("div");
          employeeDiv.className = "chat-employee";
          employeeDiv.innerHTML = `
                        <img src="../assets/profiles/${
                          employee.profile_image || "profile-placeholder.png"
                        }" alt="Profile">
                        <div class="employee-info">
                            <div class="employee-name">${employee.firstname} ${
            employee.lastname
          }</div>
                            <div class="latest-message">${
                              employee.latest_message || "No messages yet"
                            }</div>
                        </div>
                    `;
          employeeDiv.addEventListener("click", () => openChat(employee.id));
          chatEmployees.appendChild(employeeDiv);
        });
      })
      .catch((error) => console.error("Error fetching employee chats:", error));
  }

  // Open chat with selected employee
  function openChat(employeeId) {
    currentRecipientId = employeeId;
    document.getElementById("recipient-id").value = employeeId;
    chatList.classList.add("d-none");
    chatBox.classList.remove("d-none");
    fetchMessages();
  }

  // Fetch messages
  function fetchMessages() {
    if (!currentRecipientId) return;
    fetch(
      `../fetch_messages.php?user_id=${window.currentUserId}&recipient_id=${currentRecipientId}`
    )
      .then((response) => response.json())
      .then((data) => {
        chatMessages.innerHTML = "";
        if (data.messages) {
          data.messages.forEach((msg) => {
            const messageDiv = document.createElement("div");
            messageDiv.className = msg.isSender
              ? "admin-message-container"
              : "employee-message-container";
            messageDiv.innerHTML = `
                        ${
                          !msg.isSender
                            ? `<img src="../assets/profiles/${
                                msg.profile_image || "profile-placeholder.png"
                              }" alt="Profile" class="message-profile-img">`
                            : ""
                        }
                        <div class="${
                          msg.isSender ? "admin-message" : "employee-message"
                        }">
                            <p>${msg.message}</p>
                        </div>
                    `;
            chatMessages.appendChild(messageDiv);
          });
          chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
        }
      })
      .catch((error) => console.error("Fetch error:", error));
  }

  // Send message
  chatForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(chatForm);

    fetch("admin_dashboard.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          fetchMessages();
          document.getElementById("chat-input").value = "";
        } else {
          console.error("Error:", data.message);
        }
      })
      .catch((error) => console.error("Fetch error:", error));
  });

  // Poll for new messages
  setInterval(() => {
    if (!chatBox.classList.contains("d-none")) {
      fetchMessages();
    } else if (chatList.classList.contains("d-none")) {
      checkNewMessages();
    }
  }, 5000);

  function markMessagesAsRead() {
    fetch("../mark_messages_read.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `user_id=${window.currentUserId}`,
    });
  }

  function checkNewMessages() {
    fetch(`../check_new_messages.php?user_id=${window.currentUserId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.hasNewMessages && chatList.classList.contains("d-none")) {
          chatNotification.classList.add("active");
        }
      });
  }

  checkNewMessages();

  // Adjust chat box position
  const employeeItems = document.querySelectorAll(".chat-employee");
  employeeItems.forEach((employee) => {
    employee.addEventListener("click", () => {
      chatBox.style.top = "120px"; // Move it further down
      chatBox.classList.remove("d-none"); // Show the chat box
    });
  });

  // Close the chat box when clicking outside
  document.addEventListener("click", (event) => {
    if (
      !chatBox.contains(event.target) &&
      !event.target.closest(".chat-employee")
    ) {
      chatBox.classList.add("d-none"); // Hide the chat box
    }
  });
});

// Function for action modal (previously defined as window.showActionModal)
function showActionModal(userId, action) {
  document.getElementById("actionUserId").value = userId;
  document.getElementById("actionType").value = action;
  const modal = new bootstrap.Modal(document.getElementById("actionModal"));
  modal.show();
}
