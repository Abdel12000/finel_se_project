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

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_SESSION["user_email"];
    $title = mysqli_real_escape_string($connect, $_POST["title"]);
    $subject = mysqli_real_escape_string($connect, $_POST["subject"]);

    $user_query = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$email' LIMIT 1");
    $user = mysqli_fetch_assoc($user_query);
    $sender_id = $user['user_id'] ?? 0;

    if ($sender_id && !empty($title) && !empty($subject)) {
        $insert_query = "INSERT INTO notifications (sender_id, title, body) VALUES ('$sender_id', '$title', '$subject')";
        if (mysqli_query($connect, $insert_query)) {
            $success_message = "Your message has been sent successfully.";
        } else {
            $error_message = "Failed to send message.";
        }
    } else {
        $error_message = "Missing sender or message information.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact us</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="templatesjungle">
    <meta name="keywords" content="plant shop">
    <meta name="description" content="Free Plant Selling Website Template">

    <link rel="stylesheet" type="text/css" href="user/css/bootstrap.min.css" />
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
        <h2 class="text-center text-success mb-4">Contact Us</h2>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?php echo $_SESSION["user_email"]; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Message Title</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <textarea class="form-control" name="subject" id="subject" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Contact Us</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
