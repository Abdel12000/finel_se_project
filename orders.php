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
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/font.css">
  <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
  <link rel="stylesheet" href="css/custom.css">
  <link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
  <header class="header">
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="navbar-header">
          <a href="#" class="navbar-brand">
            <div class="brand-text brand-big text-uppercase"><strong
                class="text-primary">Dark</strong><strong>Admin</strong></div>
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
          <div class="card">
            <div class="card-header">
              <h5>Order Summary</h5>
            </div>
            <div class="card-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>Order Id</th>

                    <th>Client Name</th>
                    <th>Plant Name</th>
                    <th>Total Amount ($)</th>
                    <th>Status</th>
                    <th>Delivery Status</th>
                    <th>Bill Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  include("common/config.php");
                  $query = "
                    SELECT 
                      u.username, 
                      p.name AS plant_name, 
                      o.total_amount, 
                      o.status AS order_status, 
                      IFNULL(d.status, 'not_set') AS delivery_status, 
                      o.created_at,
                      o.order_id
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    JOIN order_items oi ON o.order_id = oi.order_id
                    JOIN plants p ON oi.plant_id = p.plant_id
                    LEFT JOIN deliveries d ON d.order_id = o.order_id
                    ORDER BY o.created_at DESC
                  ";
                  $result = mysqli_query($connect, $query);
                  if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                      ?>
                      <tr>
                        <td><?= htmlspecialchars($row['order_id']) ?></td>

                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['plant_name']) ?></td>
                        <td><?= number_format($row['total_amount'], 2) ?></td>
                        <td>
                          <?php
                          $os = $row['order_status'];
                          if ($os === 'Paid')
                            echo '<span class="badge badge-success">Paid</span>';
                          elseif ($os === 'Pending')
                            echo '<span class="badge badge-warning">Pending</span>';
                          else
                            echo '<span class="badge badge-secondary">' . htmlspecialchars($os) . '</span>';
                          ?>
                        </td>
                        <td>
                          <?php
                          $ds = strtolower($row['delivery_status']);
                          if ($ds === 'in_transit')
                            echo '<span class="badge badge-info">In Transit</span>';
                          elseif ($ds === 'delivered')
                            echo '<span class="badge badge-success">Delivered</span>';
                          else
                            echo '<span class="badge badge-secondary">Not Set</span>';
                          ?>
                        </td>
                        <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($row['created_at']))) ?></td>
                        <td>
                          <?php if ($row['delivery_status'] === 'delivered'): ?>
                            <span class="badge bg-success text-white">Delivered</span>
                          <?php else: ?>
                            <form method="post" action="update_delivery_status.php" style="display:inline;">
                              <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                              <input type="hidden" name="status" value="in_transit">
                              <button type="submit" class="btn btn-info btn-sm">In Transit</button>
                            </form>
                            <form method="post" action="update_delivery_status.php" style="display:inline;">
                              <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                              <input type="hidden" name="status" value="delivered">
                              <button type="submit" class="btn btn-success btn-sm">Delivered</button>
                            </form>
                          <?php endif; ?>

                        
                        </td>

                      </tr>
                      <?php
                    }
                  } else {
                    echo '<tr><td colspan="7" class="text-center">No orders found</td></tr>';
                  }
                  ?>
                </tbody>
              </table>
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