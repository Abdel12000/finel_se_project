<?php
session_start();
include 'common/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id = (int) ($_POST['user_id'] ?? 0);
$amount = floatval($_POST['amount'] ?? 0);
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+1 month', strtotime($start_date)));
$upload_dir = 'uploads/subscriptions/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($user_id <= 0 || $amount <= 0 || !isset($_FILES['payment_image'])) {
    die("Invalid data submitted.");
}

$image = $_FILES['payment_image'];
$image_name = basename($image['name']);
$image_tmp = $image['tmp_name'];
$image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
$allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($image_ext, $allowed_exts)) {
    die("Only image files are allowed.");
}

$new_image_name = 'sub_' . time() . '_' . uniqid() . '.' . $image_ext;
$target_path = $upload_dir . $new_image_name;

if (!move_uploaded_file($image_tmp, $target_path)) {
    die("Failed to upload image.");
}

$escaped_image = mysqli_real_escape_string($connect, $target_path);
$insert_sql = "
    INSERT INTO monthly_subscriptions (user_id, start_date, end_date, amount, sub_image)
    VALUES ($user_id, '$start_date', '$end_date', $amount, '$escaped_image')
";

if (mysqli_query($connect, $insert_sql)) {
    header("location:sell.php?message=subscribed succesfully");
} else {
    echo "Error: " . mysqli_error($connect);
}
?>
