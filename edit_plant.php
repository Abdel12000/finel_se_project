<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: login.php');
    exit;
}
include 'common/config.php';
if (!isset($_GET['id'])) {
    echo 'Plant ID is missing.';
    exit;
}
$plant_id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $desc = mysqli_real_escape_string($connect, $_POST['description']);
    $price = (float)$_POST['price'];
    $qty = (int)$_POST['stock_qty'];
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
        header('Location: plants.php?message=Plant updated successfully');
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
        <div class="card-header"><h4 class="mb-0">Update Plant</h4></div>
        <div class="card-body">
            <?php if (isset($error)) echo $error; ?>
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
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($plant['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" required><?= htmlspecialchars($plant['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= $plant['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock_qty">Stock Qty</label>
                    <input type="number" name="stock_qty" id="stock_qty" class="form-control" value="<?= $plant['stock_qty'] ?>" required>
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
  