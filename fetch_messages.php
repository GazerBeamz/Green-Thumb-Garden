<?php
require_once './includes/db.php';

header('Content-Type: application/json');

$user_id = intval($_GET['user_id']);
$recipient_id = intval($_GET['recipient_id']);

if ($user_id <= 0 || $recipient_id <= 0) {
    echo json_encode(['messages' => []]);
    exit();
}

$query = "
    SELECT m.*, u.profile_image 
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = $user_id AND m.recipient_id = $recipient_id) 
       OR (m.sender_id = $recipient_id AND m.recipient_id = $user_id) 
    ORDER BY m.created_at ASC
";
$result = mysqli_query($conn, $query);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = [
        'message' => htmlspecialchars($row['message']),
        'isSender' => $row['sender_id'] == $user_id,
        'profile_image' => $row['profile_image']
    ];
}

echo json_encode(['messages' => $messages]);
?>