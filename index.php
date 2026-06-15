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
        <div class="container-fluid">
          <div class="row">

            <!-- Total Users -->
            <div class="col-md-3 col-sm-6">
              <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                  <div class="title">
                    <div class="icon"><i class="fa fa-users"></i></div><strong>Total Users</strong>
                  </div>
                  <div class="number dashtext-1">
                    <?php
                    include "common/config.php";
                    $sql = "SELECT COUNT(*) AS total FROM users WHERE role=0";
                    $result = mysqli_query($connect, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                  </div>

                </div>
                <div class="progress progress-template">
                  <div role="progressbar" style="width: <?php echo $row["total"];?>" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"
                    class="progress-bar progress-bar-template dashbg-1"></div>
                </div>
              </div>
            </div>

            <!-- Total Orders -->
            <div class="col-md-3 col-sm-6">
              <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                  <div class="title">
                    <div class="icon"><i class="fa fa-shopping-cart"></i></div><strong>Total Orders</strong>
                  </div>
                  <div class="number dashtext-2">
                      <?php
                    include "common/config.php";
                    $sql = "SELECT COUNT(*) AS total FROM orders ";
                    $result = mysqli_query($connect, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                  </div>
                </div>
                <div class="progress progress-template">
                  <div role="progressbar" style="width: <?php echo $row["total"];?>" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"
                    class="progress-bar progress-bar-template dashbg-2"></div>
                </div>
              </div>
            </div>

            <!-- Total Equipment -->
            <div class="col-md-3 col-sm-6">
              <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                  <div class="title">
                    <div class="icon"><i class="fa fa-cogs"></i></div><strong>Total Equipment</strong>
                  </div>
                  <div class="number dashtext-3">
                      <?php
                    include "common/config.php";
                    $sql = "SELECT COUNT(*) AS total FROM equipment";
                    $result = mysqli_query($connect, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                  </div>
                </div>
                <div class="progress progress-template">
                  <div role="progressbar" style="width: <?php echo $row["total"];?>" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"
                    class="progress-bar progress-bar-template dashbg-3"></div>
                </div>
              </div>
            </div>

            <!-- Notifications -->
            <div class="col-md-3 col-sm-6">
              <div class="statistic-block block">
                <div class="progress-details d-flex align-items-end justify-content-between">
                  <div class="title">
                    <div class="icon"><i class="fa fa-bell"></i></div><strong>Notifications</strong>
                  </div>
                  <div class="number dashtext-4">
                      <?php
                    include "common/config.php";
                    $sql = "SELECT COUNT(*) AS total FROM notifications";
                    $result = mysqli_query($connect, $sql);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?>
                  </div>
                </div>
                <div class="progress progress-template">
                  <div role="progressbar" style="width: <?php echo $row["total"];?>" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"
                    class="progress-bar progress-bar-template dashbg-4"></div>
                </div>
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

</html>