<?php
session_start();
include 'common/config.php';

$user_email = $_SESSION['user_email'] ?? '';

if (!$user_email) {
    header("Location: login.php");
    exit;
}

$email_safe = mysqli_real_escape_string($connect, $user_email);
$user_query = "SELECT user_id, status FROM users WHERE email = '$email_safe' LIMIT 1";
$user_result = mysqli_query($connect, $user_query);

if (!$user_result || mysqli_num_rows($user_result) === 0) {
    echo "User not found.";
    exit;
}

$user_data = mysqli_fetch_assoc($user_result);
$user_id = (int) $user_data['user_id'];
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
                    <div class="card-body">
                        ' . ($message ? '<div class="alert alert-info">' . $message . '</div>' : '') . '
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

$subscription_check = mysqli_query($connect, "SELECT * FROM monthly_subscriptions WHERE user_id = $user_id LIMIT 1");
$is_subscribed = mysqli_num_rows($subscription_check) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryName'])) {
    $categoryName = mysqli_real_escape_string($connect, $_POST['categoryName']);
    $insertQuery = "INSERT INTO categories (user_id, name) VALUES ($user_id, '$categoryName')";
    if (mysqli_query($connect, $insertQuery)) {
        $category_message = '<div class="alert alert-success mt-3">Category added successfully.</div>';
    } else {
        $category_message = '<div class="alert alert-danger mt-3">Error adding category.</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plantCategory'])) {
    $category_id = (int) ($_POST['plantCategory']);
    $name = mysqli_real_escape_string($connect, $_POST['plantName'] ?? '');
    $qty = (int) ($_POST['stockQty'] ?? 0);
    $desc = mysqli_real_escape_string($connect, $_POST['plantDescription'] ?? '');
    $price = (float) ($_POST['plantPrice'] ?? 0);
    $imgPath = null;

    $cat = mysqli_query($connect, "SELECT 1 FROM categories WHERE category_id = $category_id AND user_id = $user_id LIMIT 1");
    if (mysqli_num_rows($cat) === 0) {
        $plant_message = '<div class="alert alert-danger mt-3">Choose a valid category.</div>';
    } else {
        $dup = mysqli_query($connect, "SELECT 1 FROM plants WHERE name = '$name' AND category_id = $category_id AND user_id = $user_id LIMIT 1");
        if (mysqli_num_rows($dup)) {
            $plant_message = '<div class="alert alert-warning mt-3">Plant already exists in this category.</div>';
        } else {
            if (!empty($_FILES['plantImage']['tmp_name']) && $_FILES['plantImage']['error'] === 0) {
                $uploadDir = 'plant/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $ext = pathinfo($_FILES['plantImage']['name'], PATHINFO_EXTENSION);
                $file = uniqid('', true) . '.' . $ext;
                $target = $uploadDir . $file;
                if (move_uploaded_file($_FILES['plantImage']['tmp_name'], $target)) {
                    $imgPath = $target;
                }
            }

            $imgSql = $imgPath ? "'" . mysqli_real_escape_string($connect, $imgPath) . "'" : 'NULL';
            $insertPlant = mysqli_query(
                $connect,
                "INSERT INTO plants (user_id, category_id, name, description, image_url, stock_qty, price)
                 VALUES ($user_id, $category_id, '$name', '$desc', $imgSql, $qty, $price)"
            );

            if ($insertPlant) {
                $plant_message = '<div class="alert alert-success mt-3">Plant added successfully.</div>';
            } else {
                $plant_message = '<div class="alert alert-danger mt-3">Error: ' . mysqli_error($connect) . '</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sell Plants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="user/css/bootstrap.min.css" />
    <link rel="stylesheet" href="user/icomoon/icomoon.css" />
    <link rel="stylesheet" href="user/css/vendor.css" />
    <link rel="stylesheet" href="user/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .container-custom {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        h5 {
            font-weight: 600;
        }

        .btn-success {
            background-color: #2ecc71;
            border-color: #27ae60;
            transition: background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #27ae60;
            border-color: #219150;
        }

        table thead {
            background-color: #2ecc71;
            color: white;
        }
    </style>
</head>

<body>
    <?php include("user/header.php"); ?>
    <div class="container-custom">

        <?php if (!$is_subscribed): ?>
            <div class="text-center my-5">
                <p class="lead">You are not subscribed yet.</p>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#subscribeModal">Subscribe
                    Now</button>
            </div>
        <?php else: ?>

            <div class="mb-5">
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
                <h5>Add Category</h5>
                <form method="post" class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <input type="text" name="categoryName" class="form-control" placeholder="Enter category name"
                            required />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">Add Category</button>
                    </div>
                </form>
                <?php if (!empty($category_message))
                    echo $category_message; ?>
            </div>

            <div class="mb-5">
                <h5>Category List</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT category_id, name FROM categories WHERE user_id = $user_id ORDER BY name";
                            $result = mysqli_query($connect, $query);
                            if (mysqli_num_rows($result) > 0):
                                while ($row = mysqli_fetch_assoc($result)):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td>
                                            <a href="update_user_category.php?id=<?= $row['category_id'] ?>"
                                                class="btn btn-primary btn-sm me-1">Update</a>
                                            <a href="delete_user_category.php?id=<?= $row['category_id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                endwhile;
                            else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-5">
                <h5>Add Plant</h5>
                <form method="post" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-4">
                        <label for="plantCategory" class="form-label">Category</label>
                        <select id="plantCategory" name="plantCategory" class="form-select" required>
                            <option selected disabled>Select Category</option>
                            <?php
                            $cats = mysqli_query($connect, "SELECT category_id, name FROM categories WHERE user_id = $user_id ORDER BY name");
                            while ($c = mysqli_fetch_assoc($cats)) {
                                echo '<option value="' . $c['category_id'] . '">' . htmlspecialchars($c['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="plantName" class="form-label">Plant Name</label>
                        <input type="text" id="plantName" name="plantName" class="form-control" required />
                    </div>
                    <div class="col-md-4">
                        <label for="stockQty" class="form-label">Stock Quantity</label>
                        <input type="number" id="stockQty" name="stockQty" class="form-control" required />
                    </div>
                    <div class="col-12">
                        <label for="plantDescription" class="form-label">Description</label>
                        <textarea id="plantDescription" name="plantDescription" rows="2" class="form-control"
                            required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="plantPrice" class="form-label">Price ($)</label>
                        <input type="number" id="plantPrice" name="plantPrice" step="0.01" class="form-control" required />
                    </div>
                    <div class="col-md-6">
                        <label for="plantImage" class="form-label">Image</label>
                        <input type="file" id="plantImage" name="plantImage" class="form-control" accept="image/*"
                            required />
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success w-100">Add Plant</button>
                    </div>
                </form>
                <?php if (!empty($plant_message))
                    echo $plant_message; ?>
            </div>

            <div>
                <h5>Plant List</h5>
                <div class="table-responsive">
                    <?php
                    $query = "
                SELECT p.image_url, p.name, c.name AS category_name, p.plant_id, p.description, p.price, p.stock_qty
                FROM plants p
                JOIN categories c ON p.category_id = c.category_id
                WHERE p.user_id = $user_id
                ORDER BY p.name
            ";
                    $result = mysqli_query($connect, $query);
                    ?>
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th style="width: 100px;">Price ($)</th>
                                <th style="width: 80px;">Stock Qty</th>
                                <th style="width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="plant" width="50" />
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td><?= number_format($row['price'], 2) ?></td>
                                    <td><?= (int) $row['stock_qty'] ?></td>
                                    <td>
                                        <a href="edit_user_plant.php?id=<?= $row['plant_id'] ?>"
                                            class="btn btn-primary btn-sm me-1">Update</a>
                                        <a href="delete_user_plants.php?id=<?= $row['plant_id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($result) === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No plants found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="subscribe.php" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subscribeModalLabel">Subscribe to Monthly Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (USD)</label>
                        <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount"
                            value="20" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_image" class="form-label">Upload Payment Image</label>
                        <input type="file" name="payment_image" id="payment_image" class="form-control" accept="image/*"
                            required>
                    </div>
                    <p class="text-muted small mt-2">Please upload a screenshot or image as proof of payment.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm Subscription</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>