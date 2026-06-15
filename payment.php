<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
  header("location:login.php");
  exit;
}

include "common/config.php";

$end_date = date("Y-m-d");
$start_date = date("Y-m-d", strtotime("-2 months"));
$whereClause = " WHERE p.created_at BETWEEN '$start_date' AND '$end_date' ";

$query = "
  SELECT p.payment_id, u.username, p.amount, p.status, p.payment_image, p.created_at
  FROM payments p
  JOIN users u ON u.user_id = p.user_id
 
  ORDER BY p.created_at DESC
";

$rs = mysqli_query($connect, $query);
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
  <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
  <link rel="stylesheet" href="css/custom.css">
</head>

<body>
  <header class="header">
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="navbar-header">
          <a href="#" class="navbar-brand">
            <div class="brand-text brand-big visible text-uppercase">
              <strong class="text-primary">Dark</strong><strong>Admin</strong>
            </div>
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
              <h5>Payment Table (Last 2 Months)</h5>
            </div>
            <div class="card-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>id</th>
                    <th>Name</th>
                    <th>Amount Paid ($)</th>
                    <th>Paid Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (mysqli_num_rows($rs) > 0) {
                    while ($row = mysqli_fetch_assoc($rs)) {
                      echo "<tr>";
                      echo "<td>" . number_format($row['payment_id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                      echo "<td>" . number_format($row['amount'], 2) . "</td>";
                      echo "<td>Paid</td>";
                      echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                      echo "<td><a href='payment_details.php?id=" . (int) $row['payment_id'] . "' class='btn btn-info btn-sm'>Show Details</a></td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='6' class='text-center'>No available payment</td></tr>";
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
</body>

</html>
