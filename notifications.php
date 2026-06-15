<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin dashboard</title>
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
                <div class="container-fluid mt-4">

                    <!-- Send Notification Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Send Notification</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            include 'common/config.php';
                            $message = '';

                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $user_id = (int) ($_POST['user_id'] ?? 0);
                                $title = mysqli_real_escape_string($connect, $_POST['title'] ?? '');
                                $content = mysqli_real_escape_string($connect, $_POST['content'] ?? '');

                                if ($user_id && $title && $content) {
                                    $check_sql = "SELECT COUNT(*) as count FROM feedback WHERE user_id = $user_id AND message = '$content' AND admin_response = '$title'";
                                    $check_result = mysqli_query($connect, $check_sql);
                                    $count_data = mysqli_fetch_assoc($check_result);

                                    if ($count_data['count'] > 0) {
                                        $message = '<div class="alert alert-warning">This message has already been sent.</div>';
                                    } else {
                                        $insert_sql = "INSERT INTO feedback (user_id, message, admin_response) VALUES ($user_id, '$content', '$title')";
                                        if (mysqli_query($connect, $insert_sql)) {
                                            $message = '<div class="alert alert-success">notification sent successfully.</div>';
                                        } else {
                                            $message = '<div class="alert alert-danger">Failed to send feedback.</div>';
                                        }
                                    }
                                } else {
                                    $message = '<div class="alert alert-danger">All fields are required.</div>';
                                }
                            }
                            ?>

                            <?= $message ?>

                            <form method="POST">
                                <div class="form-group">
                                    <label for="userSelect">Select User</label>
                                    <select class="form-control" id="userSelect" name="user_id" required>
                                        <option selected disabled>-- Choose User --</option>
                                        <?php
                                        $result = mysqli_query($connect, "SELECT user_id, username ,role FROM users WHERE role != 1 ORDER BY username ASC");
                                        $role = "";
                                        while ($user = mysqli_fetch_assoc($result)) {
                                             //if($user["role"]==2){
                                              //  $role="employe";
                                                
                                            // }
                                            // elseif($user["role"]==0){
                                              //  $role="user";
                                            // }
                                                
                                            echo '<option value="' . $user['user_id'] . '">' . htmlspecialchars($user['username']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="notifTitle">Title</label>
                                    <input type="text" class="form-control" id="notifTitle" name="title"
                                        placeholder="Enter notification title" required>
                                </div>

                                <div class="form-group">
                                    <label for="notifContent">Content</label>
                                    <textarea class="form-control" id="notifContent" name="content" rows="3"
                                        placeholder="Enter notification message" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>

                    </div>

                    <!-- Notifications Table -->
                    <?php
                    include("common/config.php");
                    $query = "
SELECT n.title, n.body, u.username, u.user_id,u.role
FROM notifications n 
JOIN users u ON n.sender_id = u.user_id 
ORDER BY n.notification_id DESC
";
                    $result = mysqli_query($connect, $query);
                    
                    ?>

                    <div class="card">
                        <div class="card-header">
                            <h5>Sent Notifications</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <?php if (isset($_GET["message"])) { ?>
                                <div id="msgBox"
                                    style="padding:10px; background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:15px;">
                                    <?= htmlspecialchars($_GET["message"]) ?>
                                </div>
                                <script>
                                    setTimeout(function () {
                                        var msg = document.getElementById('msgBox');
                                        if (msg) {
                                            msg.style.display = 'none';
                                        }
                                    }, 4000);
                                </script>
                            <?php } ?>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>User Name</th>
                                        <th>Title</th>
                                        <th>Body</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     $ro="";
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                           
                   // if($row["role"]==2){
                       // $ro="employee";
                   // }
                    //elseif($row["role"]==0){
                     //   $ro="user";
                    //}
                    //else{
                       // $ro="admin";
                   // }
                                            echo '<tr>
                    <td>' . htmlspecialchars($row['username']) . '</td>
                    <td>' . htmlspecialchars($row['title']) . '</td>
                    <td>' . htmlspecialchars($row['body']) . '</td>
                    <td><a href="reply.php?user_id=' . urlencode($row['user_id']) . '" class="btn btn-info btn-sm">Reply</a></td>
                  </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">No notifications found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
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

</html>