<?php
session_start();
if (!isset($_SESSION["user_email"])) {
    header("location:login.php");
    exit;
}

include("common/config.php");

$user_email = $_SESSION["user_email"];
$query = "SELECT * FROM users WHERE email = '$user_email' LIMIT 1";
$result = mysqli_query($connect, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = mysqli_real_escape_string($connect, $_POST['username']);
    $new_email = mysqli_real_escape_string($connect, $_POST['email']);
    $new_phone = mysqli_real_escape_string($connect, $_POST['phone']);
    $new_address = mysqli_real_escape_string($connect, $_POST['address']);
    $new_password = trim($_POST['password']);

    $update_query = "UPDATE users SET 
        username = '$new_username', 
        email = '$new_email', 
        phone = '$new_phone', 
        address = '$new_address'";

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query .= ", password_hash = '$hashed_password'";
    }
    $update_query .= " WHERE email = '$user_email'";

    if (mysqli_query($connect, $update_query)) {
        $_SESSION["user_email"] = $new_email;
        echo "<script>alert('Information updated successfully.'); window.location.href='user_dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Update failed.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update User Information</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="user/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="user/icomoon/icomoon.css">
    <link rel="stylesheet" type="text/css" href="user/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="user/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet">
</head>

<body>
    <?php include("user/header.php"); ?>

    <div class="container mt-5">
        <h2 class="mb-4">Update Your Information</h2>
        <form method="POST">
            <div class="form-group mb-3">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" required value="<?= htmlspecialchars($user['username']) ?>">
            </div>

            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" required value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="form-group mb-3">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>

            <div class="form-group mb-3">
                <label for="address">Address</label>
                <textarea class="form-control" name="address" id="address" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="password">New Password <small>(Leave blank to keep current)</small></label>
                <input type="password" class="form-control" name="password" id="password">
            </div>

            <button type="submit" class="btn btn-success">Update Information</button>
        </form>
    </div>

</body>
</html>
