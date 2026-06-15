<?php
session_start();
if (!isset($_SESSION["user_email"])) {
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
        header("Location: sell.php?message=Category updated successfully");
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
    <title>Plantly - Free Plant Selling Website Template</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="templatesjungle">
    <meta name="keywords" content="plant shop">
    <meta name="description" content="Free Plant Selling Website Template">

    <link rel="stylesheet" type="text/css" href="user/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="user/icomoon/icomoon.css">
    <link rel="stylesheet" type="text/css" href="user/css/vendor.css">

    <link rel="stylesheet" type="text/css" href="user/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet">
</head>

<body>
    <?php include("user/header.php"); ?>

    <div class="d-flex align-items-stretch">
        <div class="page-content">
          
            <section class="no-padding-top no-padding-bottom">
                <div class="container-fluid">
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="mb-0">Update Category</h4>
                        </div>
                        <div class="card-body">

                            <?php if (isset($error))
                                echo $error; ?>

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