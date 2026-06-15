<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("location:login.php");
    exit;
}
include "common/config.php";

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$payment_query = mysqli_query($connect, "SELECT * FROM payments WHERE payment_id = $payment_id LIMIT 1");
$payment = mysqli_fetch_assoc($payment_query);

$user_id = $payment['user_id'];
$order_id = $payment['order_id'];
$user_query = mysqli_query($connect, "SELECT * FROM users WHERE user_id = $user_id LIMIT 1");
$user = mysqli_fetch_assoc($user_query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Details</title>
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
                    <div class="brand-text brand-big"><strong class="text-primary">Dark</strong><strong>Admin</strong></div>
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
                <h2 class="h5 no-margin-bottom">Payment Details</h2>
            </div>
        </div>
        <section class="no-padding-top no-padding-bottom">
            <div class="container-fluid">
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Details for Payment ID: BILL-<?php echo strtoupper(substr(md5($payment_id . $payment['created_at']), 0, 8)); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                                <p><strong>Address:</strong> <?php echo $user['address']; ?></p>
                                <p><strong>Status:</strong> <?php echo $payment['status']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Amount Paid:</strong> $<?php echo number_format($payment['amount'], 2); ?></p>
                                <p><strong>Payment Date:</strong> <?php echo $payment['created_at']; ?></p>
                                <p><strong>Receipt:</strong><br>
                                    <img src="<?php echo $payment['payment_image']; ?>" class="img-thumbnail" style="width:120px;">
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Order Items</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php
                                $item_query = mysqli_query($connect, "SELECT oi.quantity, pl.name, pl.description, pl.price, pl.stock_qty 
                                                                     FROM order_items oi 
                                                                     JOIN plants pl ON pl.plant_id = oi.plant_id 
                                                                     WHERE oi.order_id = $order_id");
                                if (mysqli_num_rows($item_query) > 0) {
                                    echo "<div class='table-responsive'><table class='table table-striped table-bordered mb-0'>";
                                    echo "<thead class='thead-dark'><tr>
                                            <th>#</th>
                                            <th>Plant Name</th>
                                            <th>Description</th>
                                            <th>Price ($)</th>
                                            <th>In Stock</th>
                                            <th>Ordered Qty</th>
                                          </tr></thead><tbody>";
                                    $i = 1;
                                    while ($item = mysqli_fetch_assoc($item_query)) {
                                        echo "<tr>
                                                <td>{$i}</td>
                                                <td>{$item['name']}</td>
                                                <td>{$item['description']}</td>
                                                <td>" . number_format($item['price'], 2) . "</td>
                                                <td>{$item['stock_qty']}</td>
                                                <td>{$item['quantity']}</td>
                                              </tr>";
                                        $i++;
                                       
                                    }
                                    echo "</tbody></table></div>";
                                } else {
                                    echo "<div class='p-3'><p class='text-muted'>No order items found.</p></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/popper.js/umd/popper.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="js/front.js"></script>
</body>
</html>

