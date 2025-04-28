<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

// Fetch recent login and logout activities of employees
$query = "
    SELECT u.firstname, u.lastname, u.profile_image, l.type, l.login_time 
    FROM login_logs l
    JOIN users u ON l.user_id = u.id
    WHERE u.role = 'employee'
    ORDER BY l.login_time DESC
    LIMIT 10
";
$result = mysqli_query($conn, $query);

$activities = [];
while ($row = mysqli_fetch_assoc($result)) {
    $activities[] = [
        'firstname' => $row['firstname'],
        'lastname' => $row['lastname'],
        'profile_image' => $row['profile_image'] ?: 'profile-placeholder.png',
        'type' => $row['type'],
        'time' => date('h:i A, M d', strtotime($row['login_time']))
    ];
}

echo json_encode($activities);
?>