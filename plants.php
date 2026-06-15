<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("location:login.php");
    exit;
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
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="navbar-header">
                    <a href="" class="navbar-brand">
                        <div class="brand-text brand-big visible text-uppercase"><strong class="text-primary">Dark</strong><strong>Admin</strong></div>
                        <div class="brand-text brand-sm"><strong class="text-primary">D</strong><strong>A</strong></div>
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
                <div class="container-fluid mt-4">
                    <div class="card mb-4">
                        <?php if (isset($_GET["message"])) { ?>
                            <div id="msgBox" class="alert alert-success">
                                <?= htmlspecialchars($_GET["message"]) ?>
                            </div>
                            <script>
                                setTimeout(() => document.getElementById('msgBox').style.display = 'none', 4000);
                            </script>
                        <?php } ?>
                        <div class="card-header">
                            <h5>Add Category</h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label for="categoryName">Category Name</label>
                                    <input type="text" name="categoryName" class="form-control" id="categoryName" required>
                                </div>
                                <button type="submit" name="addCategory" class="btn btn-success">Add Category</button>
                            </form>
                            <?php
                            include("common/config.php");
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addCategory'])) {
                                $categoryName = mysqli_real_escape_string($connect, $_POST['categoryName']);
                                $insertQuery = "INSERT INTO categories (name) VALUES ('$categoryName')";
                                if (mysqli_query($connect, $insertQuery)) {
                                    echo '<div class="alert alert-success mt-3">Category added successfully.</div>';
                                } else {
                                    echo '<div class="alert alert-danger mt-3">Error adding category.</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Category List</h5>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr><th>Category Name</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT category_id, name FROM categories";
                                    $result = mysqli_query($connect, $query);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>
                                                <td>' . htmlspecialchars($row['name']) . '</td>
                                                <td>
                                                    <a href="update_category.php?id=' . $row['category_id'] . '" class="btn btn-primary btn-sm">Update</a>
                                                    <a href="delete_category.php?id=' . $row['category_id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\');">Delete</a>
                                                </td>
                                            </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="2" class="text-center">No categories found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPlant'])) {
                        $category_id = isset($_POST['plantCategory']) ? (int) $_POST['plantCategory'] : 0;
                        $name = mysqli_real_escape_string($connect, $_POST['plantName'] ?? '');
                        $qty = (int) ($_POST['stockQty'] ?? 0);
                        $desc = mysqli_real_escape_string($connect, $_POST['plantDescription'] ?? '');
                        $price = (float) ($_POST['plantPrice'] ?? 0);
                        $imgPath = null;

                        $cat = mysqli_query($connect, "SELECT 1 FROM categories WHERE category_id = $category_id LIMIT 1");
                        if (mysqli_num_rows($cat) === 0) {
                            echo '<div class="alert alert-danger">Choose a valid category.</div>';
                        } else {
                            $dup = mysqli_query($connect, "SELECT 1 FROM plants WHERE name = '$name' AND category_id = $category_id LIMIT 1");
                            if (mysqli_num_rows($dup)) {
                                echo '<div class="alert alert-warning">Plant already exists in this category.</div>';
                            } else {
                                if (!empty($_FILES['plantImage']['tmp_name']) && $_FILES['plantImage']['error'] === 0) {
                                    $uploadDir = 'plant/';
                                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                                    $ext = pathinfo($_FILES['plantImage']['name'], PATHINFO_EXTENSION);
                                    $file = uniqid('', true) . '.' . $ext;
                                    $target = $uploadDir . $file;
                                    if (move_uploaded_file($_FILES['plantImage']['tmp_name'], $target)) {
                                        $imgPath = $target;
                                    }
                                }
                                $imgSql = $imgPath ? "'" . mysqli_real_escape_string($connect, $imgPath) . "'" : 'NULL';
                                $insert = mysqli_query($connect, "INSERT INTO plants (category_id, name, description, image_url, stock_qty, price, status) VALUES ($category_id, '$name', '$desc', $imgSql, $qty, $price, 'active')");
                                echo $insert ? '<div class="alert alert-success">Plant added.</div>' : '<div class="alert alert-danger">Error: ' . mysqli_error($connect) . '</div>';
                            }
                        }
                    }
                    ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Add Plant</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="plantCategory">Category</label>
                                    <select name="plantCategory" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <?php
                                        $cat_result = mysqli_query($connect, "SELECT category_id, name FROM categories");
                                        while ($cat = mysqli_fetch_assoc($cat_result)) {
                                            echo '<option value="' . $cat['category_id'] . '">' . htmlspecialchars($cat['name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="plantName">Plant Name</label>
                                    <input type="text" name="plantName" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="plantDescription">Description</label>
                                    <textarea name="plantDescription" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="plantPrice">Price ($)</label>
                                    <input type="number" name="plantPrice" class="form-control" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label for="stockQty">Stock Quantity</label>
                                    <input type="number" name="stockQty" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="plantImage">Plant Image</label>
                                    <input type="file" name="plantImage" class="form-control-file" accept="image/*" required>
                                </div>
                                <button type="submit" name="addPlant" class="btn btn-success">Add Plant</button>
                            </form>
                        </div>
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