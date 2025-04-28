document.addEventListener('DOMContentLoaded', () => {
    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    const chatClose = document.getElementById('chat-close');
    const chatNotification = document.getElementById('chat-notification');

    chatToggle.addEventListener('click', () => {
        chatBox.classList.toggle('d-none');
        if (!chatBox.classList.contains('d-none')) {
            chatNotification.classList.remove('active');
            markMessagesAsRead();
            fetchMessages();
        }
    });

    chatClose.addEventListener('click', () => {
        chatBox.classList.add('d-none');
    });

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(chatForm);

        fetch('employee_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    fetchMessages();
                    document.getElementById('chat-input').value = '';
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Fetch error:', error));
    });

    function fetchMessages() {
        fetch(`../fetch_messages.php?user_id=${userId}&recipient_id=${adminId}`)
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML = '';
                if (data.messages) {
                    data.messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = msg.isSender ? 'employee-message' : 'admin-message';
                        messageDiv.innerHTML = `<p>${msg.message}</p>`;
                        chatMessages.appendChild(messageDiv);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            })
            .catch(error => console.error('Fetch error:', error));
    }

    function markMessagesAsRead() {
        fetch('../mark_messages_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `user_id=${userId}`
        });
    }

    function checkNewMessages() {
        fetch(`../check_new_messages.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.hasNewMessages && chatBox.classList.contains('d-none')) {
                    chatNotification.classList.add('active');
                }
            });
    }

    setInterval(() => {
        if (!chatBox.classList.contains('d-none')) {
            fetchMessages();
        } else {
            checkNewMessages();
        }
    }, 5000);

    checkNewMessages();
});