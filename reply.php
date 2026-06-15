<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("location:login.php");
    exit;
}
include("common/config.php");

$user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
if ($user_id <= 0) {
    die("Invalid user.");
}

$previous_message = '';
$feedback_exists = false;
$admin_response = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_response = trim($_POST['admin_response'] ?? '');
    $check_sql = "SELECT COUNT(*) as cnt FROM feedback WHERE user_id = $user_id AND admin_response = '" . mysqli_real_escape_string($connect, $admin_response) . "'";
    $check_res = mysqli_query($connect, $check_sql);
    $count_row = mysqli_fetch_assoc($check_res);
    if ($count_row['cnt'] > 0) {
        $feedback_exists = true;
    } else {
        $msg_sql = "SELECT body FROM notifications WHERE sender_id = $user_id ORDER BY notification_id DESC LIMIT 1";
        $msg_res = mysqli_query($connect, $msg_sql);
        if ($msg_res && mysqli_num_rows($msg_res) > 0) {
            $prev_row = mysqli_fetch_assoc($msg_res);
            $previous_message = $prev_row['body'];
        }
        $admin_response_esc = mysqli_real_escape_string($connect, $admin_response);
        $prev_msg_esc = mysqli_real_escape_string($connect, $previous_message);
        $insert_sql = "INSERT INTO feedback (user_id, message, admin_response) VALUES ($user_id, '$prev_msg_esc', '$admin_response_esc')";
        mysqli_query($connect, $insert_sql);
        header("Location: notifications.php?message=reply send succesfully");
        exit;
    }
} else {
    $msg_sql = "SELECT body FROM notifications WHERE sender_id = $user_id ORDER BY notification_id DESC LIMIT 1";
    $msg_res = mysqli_query($connect, $msg_sql);
    if ($msg_res && mysqli_num_rows($msg_res) > 0) {
        $prev_row = mysqli_fetch_assoc($msg_res);
        $previous_message = $prev_row['body'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reply to User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <!-- Custom Font Icons CSS-->
    <link rel="stylesheet" href="css/font.css">
    <!-- Google fonts - Muli-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/favicon.ico">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="search-panel">
                <div class="search-inner d-flex align-items-center justify-content-center">
                    <div class="close-btn">Close <i class="fa fa-close"></i></div>
                    <form id="searchForm" action="#">
                        <div class="form-group">
                            <input type="search" name="search" placeholder="What are you searching for...">
                            <button type="submit" class="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="navbar-header">
                    <!-- Navbar Header--><a href="" class="navbar-brand">
                        <div class="brand-text brand-big visible text-uppercase"><strong
                                class="text-primary">Dark</strong><strong>Admin</strong></div>
                        <div class="brand-text brand-sm"><strong class="text-primary">D</strong><strong>A</strong></div>
                    </a>
                    <!-- Sidebar Toggle Btn-->
                    <button class="sidebar-toggle"><i class="fa fa-long-arrow-left"></i></button>
                </div>
                <div class="right-menu list-inline no-margin-bottom">

                    <!-- Tasks-->


                    <!-- Log out               -->
                    <div class="list-inline-item logout"> <a id="logout" href="logout.php" class="nav-link">Logout <i
                                class="icon-logout"></i></a></div>
                </div>
            </div>
        </nav>
    </header>
    <div class="d-flex align-items-stretch">
        <!-- Sidebar Navigation-->
        <?php include("common/admin_nav.php"); ?>

        <!-- Sidebar Navigation end-->
        <div class="page-content">
            <div class="page-header">
                <div class="container-fluid">
                    <h2 class="h5 no-margin-bottom">Dashboard</h2>
                </div>
            </div>
            <section class="no-padding-top no-padding-bottom">
                <div class="container-fluid">
                    <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Reply to User #<?= htmlspecialchars($user_id) ?></h3>
            </div>
            <div class="card-body">
                <?php if ($feedback_exists): ?>
                    <div class="alert alert-danger" role="alert">
                        You have already sent this exact reply to this user.
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        Reply sent successfully!
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="mb-3">
                        <label for="previousMessage" class="form-label">Previous Message</label>
                        <textarea id="previousMessage" class="form-control" rows="4"
                            readonly><?= htmlspecialchars($previous_message) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="adminResponse" class="form-label">Your Response</label>
                        <textarea id="adminResponse" name="admin_response" class="form-control" rows="5"
                            required><?= htmlspecialchars($admin_response) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div> 
                </div>
        </div>
        </section>





    </div>
    </div>
    <!-- JavaScript files-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper.js/umd/popper.min.js"> </script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"> </script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="js/charts-home.js"></script>
    <script src="js/front.js"></script>
</body>

-->