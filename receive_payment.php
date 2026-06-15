<?php
session_start();
if (!isset($_SESSION["user_email"])) {
    header("location:login.php");
    exit;
}

include "common/config.php";

// Get user_id from email
$user_email = $_SESSION['user_email'];
$user_query = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email'");
$user_data = mysqli_fetch_assoc($user_query);
$_user_id = $user_data['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

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

    <section class="no-padding-top no-padding-bottom">
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Table</h5>
                </div>

                <?php
                $query = "
                    SELECT 
                        p.payment_image,
                        p.method,
                        p.amount,
                        p.address,
                        o.order_id,
                        pl.name AS plant_name,
                        u.username AS buyer_name
                    FROM payments p
                    JOIN orders o ON p.order_id = o.order_id
                    JOIN order_items oi ON o.order_id = oi.order_id
                    JOIN plants pl ON oi.plant_id = pl.plant_id
                    JOIN users u ON o.user_id = u.user_id
                    WHERE pl.user_id = '$_user_id'
                   
                ";

                $rs = mysqli_query($connect, $query);
                ?>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Plant</th>
                                <th>Buyer</th>
                                <th>Method</th>
                                <th>Address</th>
                                <th>Amount Paid ($)</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($rs) > 0) {
                                while ($row = mysqli_fetch_assoc($rs)) {
                                    echo "<tr>";
                                    echo "<td>{$row['plant_name']}</td>";
                                    echo "<td>{$row['buyer_name']}</td>";
                                    echo "<td>{$row['method']}</td>";
                                    echo "<td>{$row['address']}</td>";
                                    echo "<td>" . number_format($row['amount'], 2) . "</td>";
                                    echo "<td><img src='{$row['payment_image']}' alt='Receipt' style='width:50px; height:auto; object-fit:cover;'></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No payments found for your plants</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>

</body>

</html>