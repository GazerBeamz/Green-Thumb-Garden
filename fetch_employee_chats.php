<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

$query = "
    SELECT u.id, u.firstname, u.lastname, u.profile_image, 
           (SELECT message FROM messages 
            WHERE (sender_id = u.id AND recipient_id = {$_SESSION['user_id']}) 
               OR (sender_id = {$_SESSION['user_id']} AND recipient_id = u.id) 
            ORDER BY created_at DESC LIMIT 1) AS latest_message
    FROM users u
    WHERE u.role = 'employee'
";
$result = mysqli_query($conn, $query);

$employees = [];
while ($row = mysqli_fetch_assoc($result)) {
    $employees[] = [
        'id' => $row['id'],
        'firstname' => $row['firstname'],
        'lastname' => $row['lastname'],
        'profile_image' => $row['profile_image'],
        'latest_message' => $row['latest_message']
    ];
}

echo json_encode($employees);
?>