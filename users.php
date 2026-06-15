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
    <title>Admin dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/font.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="shortcut icon" href="img/favicon.ico">
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
                <a href="" class="navbar-brand">
                    <div class="brand-text brand-big visible text-uppercase"><strong class="text-primary">Dark</strong><strong>Admin</strong></div>
                    <div class="brand-text brand-sm"><strong class="text-primary">D</strong><strong>A</strong></div>
                </a>
                <button class="sidebar-toggle"><i class="fa fa-long-arrow-left"></i></button>
            </div>
            <div class="right-menu list-inline no-margin-bottom">
                <div class="list-inline-item logout"> <a id="logout" href="logout.php" class="nav-link">Logout <i class="icon-logout"></i></a></div>
            </div>
        </div>
    </nav>
</header>
<div class="d-flex align-items-stretch">
    <?php include("common/admin_nav.php"); ?>
    <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <h2 class="h5 no-margin-bottom">Dashboard</h2>
            </div>
        </div>
        <section class="no-padding-top no-padding-bottom">
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Manage Users</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET["message"])) { ?>
                            <div id="msgBox" style="padding:10px; background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:15px;">
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

                        <div class="table-responsive">
                            <?php
                            include "common/config.php";

                            $msg = '';

                            if (isset($_POST['activate'], $_POST['user_id'])) {
                                $id = (int) $_POST['user_id'];
                                if (mysqli_query($connect, "UPDATE users SET status='active' WHERE user_id=$id")) {
                                    $msg = 'User activated successfully';
                                }
                            }

                            if (isset($_POST['deactivate'], $_POST['user_id'])) {
                                $id = (int) $_POST['user_id'];
                                if (mysqli_query($connect, "UPDATE users SET status='inactive' WHERE user_id=$id")) {
                                    $msg = 'User deactivated successfully';
                                }
                            }

                            if (isset($_POST['change_role'], $_POST['user_id'])) {
                                $id = (int) $_POST['user_id'];
                                if (mysqli_query($connect, "UPDATE users SET role=2 WHERE user_id=$id")) {
                                    $msg = 'User role updated to employee';
                                }
                            }

                            $result = mysqli_query($connect, "SELECT user_id, username, email, role, status FROM users WHERE role != 1");

                            if ($msg !== '') {
                                echo "<div class='alert alert-info'>$msg</div>";
                            }
                            ?>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0) { ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)) {
                                            $randomId = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                                        ?>
                                            <tr>
                                                <td><?= $randomId ?></td>
                                                <td><?=htmlspecialchars($row['username']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                                <td>
                                                    <?php
                                                    if ($row['role'] == 0) echo 'User';
                                                    elseif ($row['role'] == 1) echo 'Admin';
                                                    elseif ($row['role'] == 2) echo 'Employee';
                                                    ?>
                                                </td>
                                                <td><?= ucfirst($row['status']) ?></td>
                                                <td>
                                                    <form method="post" style="display:inline-block">
                                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                                        <button type="submit" name="activate" class="btn btn-success btn-sm" <?= $row['status'] == 'active' ? 'disabled' : '' ?>>Activate</button>
                                                    </form>
                                                    <form method="post" style="display:inline-block">
                                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                                        <button type="submit" name="deactivate" class="btn btn-warning btn-sm" <?= $row['status'] == 'inactive' ? 'disabled' : '' ?>>Deactivate</button>
                                                    </form>
                                                    <form method="post" style="display:inline-block">
                                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                                        <button type="submit" name="change_role" class="btn btn-info btn-sm" <?= $row['role'] == 2 ? 'disabled' : '' ?>>Change To Employee</button>
                                                    </form>
                                                    <a href="update.php?id=<?= $row['user_id'] ?>" class="btn btn-primary btn-sm">Change to user</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No users found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
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
