<?php
session_start();
include 'common/config.php';

$user_email = $_SESSION["user_email"];
$user_query = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);
$user_id = $user['user_id'] ?? 0;

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cancel_id"])) {
    $cancel_id = intval($_POST["cancel_id"]);
    $booking_query = mysqli_query($connect, "SELECT equipment_id FROM equipment_bookings WHERE booking_id = $cancel_id AND user_id = $user_id");
    if ($booking_query && mysqli_num_rows($booking_query) > 0) {
        $booking = mysqli_fetch_assoc($booking_query);
        $equipment_id = intval($booking['equipment_id']);
        mysqli_query($connect, "DELETE FROM equipment_bookings WHERE booking_id = $cancel_id AND user_id = $user_id");
        mysqli_query($connect, "DELETE FROM equipment WHERE equipment_id = $equipment_id");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["cancel_id"])) {
    $name = mysqli_real_escape_string($connect, $_POST["name"]);
    $description = mysqli_real_escape_string($connect, $_POST["description"]);
    $hourly_rate = floatval($_POST["hourly_rate"]);
    $start_at = mysqli_real_escape_string($connect, $_POST["start_at"]);
    $end_at = mysqli_real_escape_string($connect, $_POST["end_at"]);
    $location = mysqli_real_escape_string($connect, $_POST["location"]);

    $upload_dir = 'equipment/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = $upload_dir . $image_name;
        move_uploaded_file($image_tmp, $image_path);
    }

    $land_image_path = '';
    if (isset($_FILES['land_image']) && $_FILES['land_image']['error'] === UPLOAD_ERR_OK) {
        $land_image_name = time() . '_land_' . basename($_FILES['land_image']['name']);
        $land_image_tmp = $_FILES['land_image']['tmp_name'];
        $land_image_path = $upload_dir . $land_image_name;
        move_uploaded_file($land_image_tmp, $land_image_path);
    }

    $insert_equipment = "INSERT INTO equipment (name, description, hourly_rate, location, image, land_image) 
        VALUES ('$name', '$description', '$hourly_rate', '$location', '$image_path', '$land_image_path')";
    $equipment_result = mysqli_query($connect, $insert_equipment);

    if ($equipment_result) {
        $equipment_id = mysqli_insert_id($connect);
        $insert_booking = "INSERT INTO equipment_bookings (user_id, equipment_id, start_at, end_at) 
            VALUES ('$user_id', '$equipment_id', '$start_at', '$end_at')";
        $booking_result = mysqli_query($connect, $insert_booking);
        if ($booking_result) {
            $success_message = "Equipment booked successfully.";
        } else {
            $error_message = "Booking failed.";
        }
    } else {
        $error_message = "Equipment insertion failed.";
    }
}

$booking_query = "
    SELECT eb.booking_id, e.name, e.description, e.hourly_rate, eb.start_at, eb.end_at, e.location, e.image
    FROM equipment_bookings eb
    JOIN equipment e ON eb.equipment_id = e.equipment_id
    WHERE eb.user_id = $user_id
    ORDER BY eb.start_at DESC
";
$booking_result = mysqli_query($connect, $booking_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Book Equipment</title>
    <link rel="stylesheet" href="user/css/bootstrap.min.css" />
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

<div class="container my-5">
    <h2 class="text-center text-success mb-4">Book Equipment</h2>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="border p-4 rounded shadow-sm bg-light">
        <div class="mb-3">
            <label class="form-label">Equipment Name</label>
            <input type="text" class="form-control" name="name" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Hourly Rate ($)</label>
            <input type="number" step="0.01" class="form-control" name="hourly_rate" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" class="form-control" name="location" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Equipment Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Land Image</label>
            <input type="file" name="land_image" class="form-control" accept="image/*" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Start At</label>
            <input type="datetime-local" class="form-control" name="start_at" required />
        </div>
        <div class="mb-3">
            <label class="form-label">End At</label>
            <input type="datetime-local" class="form-control" name="end_at" required />
        </div>
        <button type="submit" class="btn btn-success w-100">Book Equipment</button>
    </form>

    <h4 class="text-center text-primary my-5">Your Booked Equipment</h4>

    <?php if (mysqli_num_rows($booking_result) > 0): ?>
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Rate/hr ($)</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Hours</th>
                    <th>Subtotal ($)</th>
                    <th>Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                while ($row = mysqli_fetch_assoc($booking_result)):
                    $start = strtotime($row['start_at']);
                    $end = strtotime($row['end_at']);
                    $hours = max(1, round(($end - $start) / 3600));
                    $rate = $row['hourly_rate'];
                    $subtotal = $rate * $hours;
                    $total_price += $subtotal;
                ?>
                    <tr>
                        <td><img src="<?php echo $row['image']; ?>" width="80" height="60" /></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo number_format($rate, 2); ?></td>
                        <td><?php echo date("Y-m-d H:i", $start); ?></td>
                        <td><?php echo date("Y-m-d H:i", $end); ?></td>
                        <td><?php echo $hours; ?></td>
                        <td><?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="cancel_id" value="<?php echo $row['booking_id']; ?>" />
                                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <th colspan="8" class="text-end">Total:</th>
                    <th colspan="2">$<?php echo number_format($total_price, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <div class="alert alert-info text-center">No equipment booked yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
