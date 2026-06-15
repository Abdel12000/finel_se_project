<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

include "common/config.php";

if (!isset($_GET['id'])) {
    echo "User ID is missing.";
    exit;
}

$user_id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $role = (int) $_POST['role'];

    $update = "UPDATE users SET username='$username', email='$email', role=$role WHERE user_id=$user_id";
    if (mysqli_query($connect, $update)) {
        header("Location: users.php?message=user updated successfully");
        exit;
    } else {
        $error = "<div class='alert alert-danger'>Error updating user.</div>";
    }
}

$query = "SELECT username, email, role FROM users WHERE user_id = $user_id";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) == 0) {
    echo "User not found.";
    exit;
}

$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin dashboard</title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="all,follow" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/font.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700" />
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet" />
    <link rel="stylesheet" href="css/custom.css" />
    <link rel="shortcut icon" href="img/favicon.ico" />
</head>
<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="search-panel">
                <div class="search-inner d-flex align-items-center justify-content-center">
                    <div class="close-btn">Close <i class="fa fa-close"></i></div>
                    <form id="searchForm" action="#">
                        <div class="form-group">
                            <input type="search" name="search" placeholder="What are you searching for..." />
                            <button type="submit" class="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="navbar-header">
                    <a href="" class="navbar-brand">
                        <div class="brand-text brand-big visible text-uppercase">
                            <strong class="text-primary">Dark</strong><strong>Admin</strong>
                        </div>
                        <div class="brand-text brand-sm">
                            <strong class="text-primary">D</strong><strong>A</strong>
                        </div>
                    </a>
                    <button class="sidebar-toggle"><i class="fa fa-long-arrow-left"></i></button>
                </div>
                <div class="right-menu list-inline no-margin-bottom">
                    <div class="list-inline-item logout">
                        <a id="logout" href="logout.php" class="nav-link">Logout <i class="icon-logout"></i></a>
                    </div>
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
                            <?php if (isset($error)) echo $error; ?>
                            <form method="post">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required />
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required />
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="0" <?= $user['role'] == 0 ? 'selected' : '' ?>>User</option>
                                        <option value="2" <?= $user['role'] == 2 ? 'selected' : '' ?>>Employee</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper.js/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="js/charts-home.js"></script>
    <script src="js/front.js"></script>
</body>
</html>
