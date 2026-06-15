<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("location:login.php");
    exit;
}
if (isset($_GET["id"])) {
    $employee_id = (int) $_GET["id"];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update Employee</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            include("common/config.php");

                            $name = "";
                            $phone = "";
                            $address = "";

                            if (isset($employee_id)) {
                                $select = "SELECT * FROM employees WHERE employee_id = $employee_id LIMIT 1";
                                $result = mysqli_query($connect, $select);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $employee = mysqli_fetch_assoc($result);
                                    $name = htmlspecialchars($employee['name']);
                                    $phone = htmlspecialchars($employee['phone']);
                                    $address = htmlspecialchars($employee['address']);
                                }
                            }

                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($employee_id)) {
                                $name = mysqli_real_escape_string($connect, $_POST["name"]);
                                $phone = mysqli_real_escape_string($connect, $_POST["phone"]);
                                $address = mysqli_real_escape_string($connect, $_POST["address"]);

                                $update = "UPDATE employees SET name='$name', phone='$phone', address='$address' WHERE employee_id = $employee_id";
                                mysqli_query($connect, $update);

                               header("location:equipment.php?message=employee updated succesfully");
                            }
                            ?>

                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Employee</button>
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
