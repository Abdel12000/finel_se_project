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

$assignment_query = "
    SELECT 
        e.name AS equipment_name, 
        e.description, 
        e.hourly_rate, 
        e.location, 
        e.area,
        e.image,
        u.username AS booked_by_name, 
        u.phone AS booked_by_phone
    FROM assignments a
    INNER JOIN equipment e ON a.equipment_id = e.equipment_id
    INNER JOIN users u ON a.user_id = u.user_id
    WHERE a.employee_id = $user_id
";
$assignment_result = mysqli_query($connect, $assignment_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Plantly - Equipment Assignments</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="user/css/bootstrap.min.css" />
    <link rel="stylesheet" href="user/icomoon/icomoon.css" />
    <link rel="stylesheet" href="user/css/vendor.css" />
    <link rel="stylesheet" href="user/style.css" />
</head>
<body>

<?php include("user/employee_he.php"); ?>

<div class="container my-5">
    <div class="card shadow-sm" style="max-width: 1000px; margin: auto;">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Equipment Assignments</h5>
        </div>
        <div class="card-body">
            <h6 class="card-title">Employee: <?= $username ?></h6>
            	<?php if (isset($_GET["message"])) { ?>
					<div id="msgBox"
						style="padding:10px; background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:15px;">
						<?= htmlspecialchars($_GET["message"]) ?>
					</div>
					<script>
						setTimeout(function () {
							var msg = document.getElementById('msgBox');
							if (msg) {
								msg.style.display = 'none';
							}
						}, 4000);
					</script>
				<?php } ?>

            <?php if ($assignment_result && mysqli_num_rows($assignment_result) > 0): ?>
                <table class="table table-bordered mt-3 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Equipment Name</th>
                            <th>Description</th>
                            <th>Hourly Rate ($)</th>
                            <th>Location</th>
                            <th>Booked By</th>
                            <th>Phone</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($assignment_result)): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Equipment" width="80" height="60" style="object-fit:cover;">
                                </td>
                                <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= number_format($row['hourly_rate'], 2) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['booked_by_name']) ?></td>
                                <td><?= htmlspecialchars($row['booked_by_phone']) ?></td>
                                <td>
                                      <a href="confirm_ca.php?id=<?php echo $user_id; ?>" class="btn btn-primary btn-sm"
                                      >Confirm</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted mt-3">No assignments found for you as an employee.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer id="footer" class="bg-accent padding-xlarge">
    <div class="container">
        <div class="row">
            <div class="col-md-3 footer-intro">
                <div class="footer-menu">
                    <img src="user/images/logo.png" alt="logo" class="footer-logo" />
                    <p>Sem magna ut pharetra vitae duis eu senectus sem risus. Morbi non, semper vestibulum euismod accumsan augue.</p>
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
                        <li class="menu-item"><a href="#" class="mail-id">info@templatesjungle.com</a></li>
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

</body>
</html>
