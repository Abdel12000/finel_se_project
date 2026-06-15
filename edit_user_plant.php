<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}
include 'common/config.php';
if (!isset($_GET['id'])) {
    echo 'Plant ID is missing.';
    exit;
}
$plant_id = (int) $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int) $_POST['category_id'];
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $desc = mysqli_real_escape_string($connect, $_POST['description']);
    $price = (float) $_POST['price'];
    $qty = (int) $_POST['stock_qty'];
    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'plant/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file = uniqid('', true) . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $file)) {
            $image_sql = ", image_url='" . mysqli_real_escape_string($connect, $uploadDir . $file) . "'";
        }
    }
    $update = "UPDATE plants SET category_id=$category_id, name='$name', description='$desc', price=$price, stock_qty=$qty $image_sql WHERE plant_id=$plant_id";
    if (mysqli_query($connect, $update)) {
        header('Location: sell.php?message=Plant updated successfully');
        exit;
    } else {
        $error = "<div class='alert alert-danger'>Error updating plant.</div>";
    }
}
$plant_res = mysqli_query($connect, "SELECT * FROM plants WHERE plant_id=$plant_id LIMIT 1");
if (!mysqli_num_rows($plant_res)) {
    echo 'Plant not found.';
    exit;
}
$plant = mysqli_fetch_assoc($plant_res);
$cats = mysqli_query($connect, "SELECT category_id, name FROM categories ORDER BY name");
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
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Update Plant</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error))
                echo $error; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Select</option>
                        <?php while ($c = mysqli_fetch_assoc($cats)): ?>
                            <option value="<?= $c['category_id'] ?>" <?= $c['category_id'] == $plant['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Plant Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="<?= htmlspecialchars($plant['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"
                        required><?= htmlspecialchars($plant['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control"
                        value="<?= $plant['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock_qty">Stock Qty</label>
                    <input type="number" name="stock_qty" id="stock_qty" class="form-control"
                        value="<?= $plant['stock_qty'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="image">Image (optional)</label>
                    <input type="file" name="image" id="image" class="form-control-file">
                    <?php if ($plant['image_url']): ?>
                        <img src="<?= htmlspecialchars($plant['image_url']) ?>" alt="plant" width="80" class="mt-2">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update Plant</button>
            </form>
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