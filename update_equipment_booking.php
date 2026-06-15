<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

include "common/config.php";

if (!isset($_GET['id'])) {
    echo "Booking ID is missing.";
    exit;
}

$booking_id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_at = mysqli_real_escape_string($connect, $_POST['start_at']);
    $end_at = mysqli_real_escape_string($connect, $_POST['end_at']);

    $update = "UPDATE equipment_bookings SET start_at='$start_at', end_at='$end_at' WHERE booking_id=$booking_id";
    if (mysqli_query($connect, $update)) {
        header("Location: equipment.php?message=Booking updated successfully");
        exit;
    } else {
        $error = "<div class='alert alert-danger'>Error updating booking.</div>";
    }
}

$query = "SELECT start_at, end_at FROM equipment_bookings WHERE booking_id = $booking_id";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Booking not found.";
    exit;
}

$booking = mysqli_fetch_assoc($result);
?>
<?php
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
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update Equipment Booking</h4>
                        </div>
                        <div class="card-body">

                            <?php if (isset($error))
                                echo $error; ?>

                            <form method="post">
                                <div class="form-group">
                                    <label for="start_at">Start Date and Time</label>
                                    <input type="datetime-local" name="start_at" id="start_at" class="form-control"
                                        value="<?= date('Y-m-d\TH:i', strtotime($booking['start_at'])) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_at">End Date and Time</label>
                                    <input type="datetime-local" name="end_at" id="end_at" class="form-control"
                                        value="<?= date('Y-m-d\TH:i', strtotime($booking['end_at'])) ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Booking</button>
                            </form>

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