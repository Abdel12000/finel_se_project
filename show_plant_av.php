<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
  header("location:login.php");
}
?>
<!DOCTYPE html>
<html>

<head>
  <style></style>
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
 

</head>

<body style="background-color:white;">
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
                         <div class="card mb-4">
                        <div class="card-header">
                            <h5>Plant List</h5>
                        </div>
     
                        <div class="card-body table-responsive">
                            <?php
                            include("common/config.php");
                            $query = "SELECT p.image_url, p.name, c.name AS category_name, p.plant_id, p.description, p.price, p.stock_qty, p.status FROM plants p JOIN categories c ON p.category_id = c.category_id";
                            $result = mysqli_query($connect, $query);
                            ?>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Image</th><th>Name</th><th>Category</th><th>Description</th><th>Price ($)</th><th>Stock Qty</th><th>Status</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= $row['image_url'] ? '<img src="' . htmlspecialchars($row['image_url']) . '" width="50">' : 'N/A' ?></td>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['category_name']) ?></td>
                                            <td><?= htmlspecialchars($row['description']) ?></td>
                                            <td><?= number_format($row['price'], 2) ?></td>
                                            <td><?= (int) $row['stock_qty'] ?></td>
                                            <td><span class="badge <?= $row['status'] === 'active' ? 'badge-success' : 'badge-secondary' ?>"><?= ucfirst($row['status']) ?></span></td>
                                            <td>
                                                <a href="edit_plant.php?id=<?= $row['plant_id'] ?>" class="btn btn-primary btn-sm">Update</a>
                                                <?php if ($row['status'] === 'active'): ?>
                                                    <form method="post" action="deactivate_plant.php" style="display:inline;">
                                                        <input type="hidden" name="plant_id" value="<?= $row['plant_id'] ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Deactivate this plant?')">Deactivate</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">No actions</span>
                                                <?php endif; ?>
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