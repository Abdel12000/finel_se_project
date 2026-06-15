<?php
session_start();
include 'common/config.php';

$user_email = $_SESSION['user_email'] ?? '';

if (!$user_email) {
    header("Location: login.php");
    exit;
}

$email_safe = mysqli_real_escape_string($connect, $user_email);
$user_query = "SELECT user_id, status FROM users WHERE email = '$email_safe' LIMIT 1";
$user_result = mysqli_query($connect, $user_query);

if (!$user_result || mysqli_num_rows($user_result) === 0) {
    echo "User not found.";
    exit;
}

$user_data = mysqli_fetch_assoc($user_result);
$user_id = (int) $user_data['user_id'];
$status = $user_data['status'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'inactive') {
    $title = mysqli_real_escape_string($connect, $_POST['title'] ?? '');
    $subject = mysqli_real_escape_string($connect, $_POST['subject'] ?? '');

    if ($title && $subject) {
        $check_sql = "SELECT COUNT(*) AS count FROM notifications WHERE sender_id = $user_id AND title = '$title' AND body = '$subject'";
        $check_result = mysqli_query($connect, $check_sql);
        $count_row = mysqli_fetch_assoc($check_result);

        if ($count_row['count'] > 0) {
            $message = "You have already sent this activation request.";
        } else {
            $insert_sql = "INSERT INTO notifications (sender_id, title, body) VALUES ($user_id, '$title', '$subject')";
            if (mysqli_query($connect, $insert_sql)) {
                $message = "Notification sent successfully. Your account will be activated within 24 hours.";
            } else {
                $message = "Failed to send notification.";
            }
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

if ($status === 'inactive') {
    echo '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Account Inactive</h4>
                    </div>
                    <div class="card-body">
                        ' . ($message ? '<div class="alert alert-info">' . $message . '</div>' : '') . '
                        <form action="" method="POST">
                            <input type="hidden" name="user_id" value="' . $user_id . '">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <textarea name="subject" id="subject" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Activation Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>';
	exit;
}
?>
<?php

include("common/config.php");

$user_email = $_SESSION["user_email"];
$user_query = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);
$user_id = $user['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['read_id'])) {
    $read_id = intval($_POST['read_id']);
    $update_query = "UPDATE feedback SET is_read = 'Read' WHERE feedback_id = $read_id AND user_id = $user_id";
    mysqli_query($connect, $update_query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM feedback WHERE feedback_id = $delete_id AND user_id = $user_id";
    mysqli_query($connect, $delete_query);
}

$feedback_query = "SELECT feedback_id, message, admin_response, is_read FROM feedback WHERE user_id = $user_id ORDER BY feedback_id DESC";
$feedback_result = mysqli_query($connect, $feedback_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Notification</title>
    <link rel="stylesheet" href="user/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="user/icomoon/icomoon.css">
    <link rel="stylesheet" type="text/css" href="user/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="user/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet">
</head>

<body>

<?php include("user/employee_he.php"); ?>

<div class="container mt-5">
    <h2 class="mb-4 text-center text-success">Your Notifications</h2>
    <?php if (mysqli_num_rows($feedback_result) > 0): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php while ($row = mysqli_fetch_assoc($feedback_result)): ?>
                    <?php $is_read = ($row['is_read'] === 'Read'); ?>
                    <div class="card shadow-sm mb-4 border-<?php echo $is_read ? 'secondary' : 'success'; ?>">
                        <div class="card-body">
                            <h5 class="card-title text-<?php echo $is_read ? 'secondary' : 'dark'; ?>">Notification #<?php echo $row['feedback_id']; ?></h5>
                            <p class="card-text">
                                <strong>title:</strong>
                                <span style="font-weight: <?php echo $is_read ? 'normal' : 'bold'; ?>;">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </span>
                            </p>
                            <p class="card-text">
                                <strong>subject:</strong>
                                <span style="font-weight: <?php echo $is_read ? 'normal' : 'bold'; ?>;">
                                    <?php echo htmlspecialchars($row['admin_response']); ?>
                                </span>
                            </p>

                            <?php if (!$is_read): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="read_id" value="<?php echo $row['feedback_id']; ?>">
                                    <button type="submit" class="btn btn-outline-success btn-sm">Mark as Read</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-secondary">Read</span>
                            <?php endif; ?>

                            <form method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                <input type="hidden" name="delete_id" value="<?php echo $row['feedback_id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                                                

                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No Notifications Found</div>
    <?php endif; ?>
</div>

</body>
</html>
