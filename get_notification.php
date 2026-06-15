<?php
session_start();
include 'common/config.php';

$user_email = $_SESSION['user_email'] ?? '';
if (!$user_email) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$email_safe = mysqli_real_escape_string($connect, $user_email);
$user_query = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$email_safe' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$user_id = (int) $user['user_id'];

$feedback_query = "
    SELECT feedback_id, message, admin_response, is_read 
    FROM feedback 
    WHERE user_id = $user_id 
    ORDER BY feedback_id DESC
";
$result = mysqli_query($connect, $feedback_query);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = [
        "id" => $row['feedback_id'],
        "title" => $row['message'],
        "body" => $row['admin_response'],
        "is_read" => $row['is_read'] === 'Read'
    ];
}

echo json_encode([
    'status' => 'success',
    'notifications' => $notifications
]);
