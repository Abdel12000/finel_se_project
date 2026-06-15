<?php
session_start();
include 'common/config.php';

$employee_email = $_SESSION['user_email'] ?? '';

if (!$employee_email) {
    header("Location: login.php");
    exit;
}

$email_safe = mysqli_real_escape_string($connect, $employee_email);
$user_query = "SELECT user_id, username, status FROM users WHERE email = '$email_safe' LIMIT 1";
$user_result = mysqli_query($connect, $user_query);

if (!$user_result || mysqli_num_rows($user_result) === 0) {
    echo "User not found.";
    exit;
}

$user_data = mysqli_fetch_assoc($user_result);
$user_id = (int) $user_data['user_id'];
$username = htmlspecialchars($user_data['username']);
$status = $user_data['status'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'inactive') {
    $title = mysqli_real_escape_string($connect, $_POST['title'] ?? '');
    $subject = mysqli_real_escape_string($connect, $_POST['subject'] ?? '');

    if ($title && $subject) {
        $check_sql = "SELECT COUNT(*) AS count FROM notifications WHERE sender_id = $user_id AND title = '$title' AND body = '$subject'";
        $check_result = mysqli_query($connect, $check_sql);
        $count_row = mysqli_fetch_assoc($check_result);

        if ($count_row['count'] > 0) {
            $message = "You have already sent this activation request.";
        } else {
            $insert_sql = "INSERT INTO notifications (sender_id, title, body) VALUES ($user_id, '$title', '$subject')";
            if (mysqli_query($connect, $insert_sql)) {
                $message = "Notification sent successfully. Your account will be activated within 24 hours.";
            } else {
                $message = "Failed to send notification.";
            }
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

if ($status === 'inactive') {
    echo '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Account Inactive</h4>
                    </div>
                    <div class="card-body">'
        . ($message ? '<div class="alert alert-info">' . $message . '</div>' : '') . '
                        <form action="" method="POST">
                            <input type="hidden" name="user_id" value="' . $user_id . '">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <textarea name="subject" id="subject" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Activation Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    exit;
}

// UPDATED: Fetch ALL salary records for the user, ordered by payment_date DESC
$salary_query = "SELECT base_salary, bonus, deductions, total_salary, salary_month, payment_date 
                 FROM employee_salary 
                 WHERE user_id = $user_id 
                 ORDER BY payment_date DESC";
$salary_result = mysqli_query($connect, $salary_query);
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <title>Plantly - Free Plant Selling Website Template</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="author" content="templatesjungle" />
    <meta name="keywords" content="plant shop" />
    <meta name="description" content="Free Plant Selling Website Template" />

    <link rel="stylesheet" href="user/css/bootstrap.min.css" />
    <link rel="stylesheet" href="user/icomoon/icomoon.css" />
    <link rel="stylesheet" href="user/css/vendor.css" />
    <link rel="stylesheet" href="user/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet" />
    <style>
        .progress {
            height: 25px;
        }
        .progress-bar-base {
            background-color: #28a745;
        }
        .progress-bar-deduction {
            background-color: #dc3545;
        }
        .salary-record {
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>

    <?php include("user/employee_he.php"); ?>

    <div class="container my-5">
        <h4 class="mb-4">Salary Summary for <strong><?= $username ?></strong></h4>

        <?php
        if ($salary_result && mysqli_num_rows($salary_result) > 0) {
            while ($salary = mysqli_fetch_assoc($salary_result)) {
                $base_salary = (float)$salary['base_salary'];
                $bonus = (float)$salary['bonus'];
                $deduction = (float)$salary['deductions'];
                $total_salary = (float)$salary['total_salary'];
                $salary_month = date("F Y", strtotime($salary['salary_month']));
                $payment_date = date("Y-m-d", strtotime($salary['payment_date']));

                // Calculate deduction percent relative to base salary
                $deductionPercent = ($base_salary > 0) ? min(($deduction / $base_salary) * 100, 100) : 0;
                ?>

                <div class="card shadow-sm salary-record" style="max-width: 400px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Salary Month: <?= $salary_month ?></h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Base Salary:</span>
                                <strong>$<?= number_format($base_salary, 2) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Bonus:</span>
                                <strong>$<?= number_format($bonus, 2) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Deduction:</span>
                                <strong>-$<?= number_format($deduction, 2) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Salary:</span>
                                <strong>$<?= number_format($total_salary, 2) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Payment Date:</span>
                                <strong><?= $payment_date ?></strong>
                            </li>
                        </ul>
                        <label>Base Salary</label>
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-base" role="progressbar" style="width: 100%"
                                aria-valuenow="<?= $base_salary ?>" aria-valuemin="0" aria-valuemax="<?= max($base_salary, 1) ?>">
                                $<?= number_format($base_salary, 2) ?>
                            </div>
                        </div>
                        <label>Deduction</label>
                        <div class="progress">
                            <div class="progress-bar progress-bar-deduction" role="progressbar" style="width: <?= $deductionPercent ?>%"
                                aria-valuenow="<?= $deduction ?>" aria-valuemin="0" aria-valuemax="<?= $base_salary ?>">
                                -$<?= number_format($deduction, 2) ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php
            }
        } else {
            echo '<p>No salary records found.</p>';
        }
        ?>
    </div>

    <footer id="footer" class="bg-accent padding-xlarge">
        <div class="container">
            <div class="row">

                <div class="col-md-3 footer-intro">
                    <div class="footer-menu">
                        <img src="user/images/logo.png" alt="logo" class="footer-logo" />
                        <p>
                            Sem magna ut pharetra vitae duis eu senectus sem risus. Morbi non, semper vestibulum
                            euismod accumsan augue.
                        </p>

                        <div class="form-content">
                            <form>
                                <input type="text" name="email" placeholder="enter your email address" />
                                <button class="btn btn-black">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="footer-menu">
                        <h5>Company</h5>
                        <ul class="menu-list">
                            <li class="menu-item"><a href="#">About</a></li>
                            <li class="menu-item"><a href="#">Our Plantations</a></li>
                            <li class="menu-item"><a href="#">Our vision</a></li>
                            <li class="menu-item"><a href="#">Installations</a></li>
                            <li class="menu-item"><a href="#">Refer a friend</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="footer-menu">
                        <h5>Support</h5>
                        <ul class="menu-list">
                            <li class="menu-item"><a href="#">Customer FAQs</a></li>
                            <li class="menu-item"><a href="#">Shipping & Returns</a></li>
                            <li class="menu-item"><a href="#">Contact Us</a></li>
                            <li class="menu-item"><a href="#">Plant Care Tips</a></li>
                            <li class="menu-item"><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="footer-menu">
                        <h5>Contact Us</h5>
                        <ul class="menu-list">
                            <li class="menu-item">Street Avenue 487, New York, USA</li>
                            <li class="menu-item">+333 346 364 366</li>
                            <li class="menu-item">
                                <a href="#" class="mail-id">info@templatesjungle.com</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <script src="user/js/jquery-3.2.1.min.js"></script>
    <script src="user/js/bootstrap.bundle.min.js"></script>
    <script src="user/js/plugins.js"></script>
    <script src="user/js/ui-easing.js"></script>
    <script src="user/js/videopopup.js"></script>
    <script src="user/js/script.js"></script>

    <script>
        var scrollToTopBtn = document.getElementById("scroll-up");
        var rootElement = document.documentElement;

        function scrollToTop() {
            rootElement.scrollTo({
                top: 0,
                behavior: "smooth",
            });
        }
        if(scrollToTopBtn) {
            scrollToTopBtn.addEventListener("click", scrollToTop);
        }
    </script>
</body>

</html>
