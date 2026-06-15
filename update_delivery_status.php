<?php
include 'common/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if ($order_id <= 0 || !in_array($status, ['in_transit', 'delivered'])) {
        http_response_code(400);
        echo 'Invalid order ID or status.';
        exit;
    }

    $status_safe = mysqli_real_escape_string($connect, $status);

    $check_delivery = mysqli_query($connect, "SELECT order_id FROM deliveries WHERE order_id = $order_id LIMIT 1");

    if ($check_delivery && mysqli_num_rows($check_delivery) > 0) {
        mysqli_query($connect, "UPDATE deliveries SET status = '$status_safe' WHERE order_id = $order_id");
    } else {
        mysqli_query($connect, "INSERT INTO deliveries (order_id, status) VALUES ($order_id, '$status_safe')");
    }

    header('Location: orders.php?message=Delivery status updated successfully');
    exit;
}

http_response_code(400);
echo 'Bad Request';
?>
