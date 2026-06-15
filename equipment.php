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
    <title>Admin dashboard </title>
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
                    <div class="card">
                        <div class="card-header">
                            <h5>Equipment List</h5>
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
                        </div>
                        <div class="card-body table-responsive">
                            <?php
                            include 'common/config.php';

                            $sql = "
    SELECT 
        e.name AS equipment_name,
        e.description AS equipment_description,
        e.hourly_rate AS equipment_hourly_rate,
        eb.start_at AS start_date,
        eb.end_at AS end_date,
        eb.booking_id
    FROM equipment_bookings eb
    JOIN equipment e ON eb.equipment_id = e.equipment_id
    ORDER BY eb.start_at DESC
";
                            $res = mysqli_query($connect, $sql);
                            ?>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>place Name</th>
                                        <th>Description</th>
                                        <th>Hourly Rate ($)</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                            <td><?= htmlspecialchars($row['equipment_description']) ?></td>
                                            <td><?= number_format($row['equipment_hourly_rate'], 2) ?></td>
                                            <td><?= htmlspecialchars($row['start_date']) ?></td>
                                            <td><?= htmlspecialchars($row['end_date']) ?></td>
                                            <td>
                                                <a href="update_equipment_booking.php?id=<?= $row['booking_id'] ?>"
                                                    class="btn btn-primary btn-sm">Update</a>
                                                <a href="add_emp.php?id=<?= $row['booking_id'] ?>"
                                                    class="btn btn-danger btn-sm">add employee</a>
                                                <a href="show_employee.php?id=<?= $row['booking_id'] ?>"
                                                    class="btn btn-warning btn-sm">Show Employee</a>

                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
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