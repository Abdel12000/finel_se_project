<?php
session_start();
include 'common/config.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['user_id'], $_POST['address']) &&
    isset($_POST['delivery_name'], $_POST['delivery_phone'], $_POST['delivery_address'],$_POST["max_qty"],$_POST["amount"])
) {
    $user_id = (int) $_POST['user_id'];
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $delivery_name = mysqli_real_escape_string($connect, $_POST['delivery_name']);
    $delivery_phone = mysqli_real_escape_string($connect, $_POST['delivery_phone']);
    $delivery_address = mysqli_real_escape_string($connect, $_POST['delivery_address']);
    $amount = mysqli_real_escape_string($connect, $_POST['amount']);
    $max_qty = mysqli_real_escape_string($connect, $_POST['max_qty']);


    $total_amount = 0;
    $order_items = [];

    $cart_items_sql = "SELECT c.plant_id, COUNT(*) AS quantity, p.price 
                       FROM cart c 
                       JOIN plants p ON c.plant_id = p.plant_id 
                       WHERE c.user_id = $user_id 
                       GROUP BY c.plant_id, p.price";
    $cart_items_result = mysqli_query($connect, $cart_items_sql);

    while ($item = mysqli_fetch_assoc($cart_items_result)) {
        $total_amount += $item['price'] * $item['quantity'];
        $order_items[] = $item;
    }

    if ($total_amount > 0) {
        $insert_order_sql = "INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $total_amount)";
        mysqli_query($connect, $insert_order_sql);
        $order_id = mysqli_insert_id($connect);
        

        foreach ($order_items as $item) {
    $plant_id = (int) $item['plant_id'];
    $quantity = (int) $item['quantity'];
    $price_each = (float) $item['price'];
    
    $insert_order_item_sql = "INSERT INTO order_items (order_id, plant_id, quantity, price_each) 
                              VALUES ($order_id, $plant_id, $max_qty, $amount)";
    mysqli_query($connect, $insert_order_item_sql);

    // 👇 تحديث الكمية المتوفرة
    $update_stock_sql = "UPDATE plants SET stock_qty = stock_qty - $max_qty WHERE plant_id = $plant_id";
    mysqli_query($connect, $update_stock_sql);
}


        $insert_delivery_sql = "INSERT INTO deliveries (order_id, name, phone, address) 
                                VALUES ($order_id, '$delivery_name', '$delivery_phone', '$delivery_address')";
        mysqli_query($connect, $insert_delivery_sql);

        mysqli_query($connect, "DELETE FROM cart WHERE user_id = $user_id");

        header('Location: user_dashboard.php?message=order and delivery confirmed');
        exit;
    } else {
        echo "Your cart is empty.";
    }
} else {
    header('Location: cart.php');
    exit;
}
