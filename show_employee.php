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
    <title>show equipment employee</title>
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
                    <?php
                    include("common/config.php");

                    if (isset($_GET['id'])) {
                        $equipment_id = (int) $_GET['id'];

                        $query = "
        SELECT DISTINCT u.username, u.email, u.phone
        FROM assignments a
        JOIN users u ON a.employee_id = u.user_id
        WHERE a.equipment_id = $equipment_id
      ";

                        $result = mysqli_query($connect, $query);

                        if (mysqli_num_rows($result) > 0) {
                            ?>
                            <div class="card">
                                <div class="card-header">
                                    <h4>Employees Assigned to This Equipment</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo "<div class='alert alert-info'>No employees found for this equipment.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>No equipment ID found in the URL.</div>";
                    }
                    ?>
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