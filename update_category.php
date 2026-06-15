<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

include "common/config.php";

if (!isset($_GET['id'])) {
    echo "Category ID is missing.";
    exit;
}

$category_id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = mysqli_real_escape_string($connect, $_POST['categoryName']);

    $update = "UPDATE categories SET name='$categoryName' WHERE category_id=$category_id";
    if (mysqli_query($connect, $update)) {
        header("Location: plants.php?message=Category updated successfully");
        exit;
    } else {
        $error = "<div class='alert alert-danger'>Error updating category.</div>";
    }
}

$query = "SELECT name FROM categories WHERE category_id = $category_id";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Category not found.";
    exit;
}

$category = mysqli_fetch_assoc($result);
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
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <!-- Your navbar content here -->
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
                            <h4 class="mb-0">Update Category</h4>
                        </div>
                        <div class="card-body">

                            <?php if (isset($error)) echo $error; ?>

                            <form method="post">
                                <div class="form-group">
                                    <label for="categoryName">Category Name</label>
                                    <input type="text" name="categoryName" id="categoryName" class="form-control" 
                                           value="<?= htmlspecialchars($category['name']) ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Category</button>
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
