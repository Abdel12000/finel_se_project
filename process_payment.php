<?php
session_start();
include 'common/config.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['method'], $_POST['amount'], $_POST['max_qty'], $_POST['address'], $_POST['user_id']) && 
    isset($_FILES['payment_image'])
) {
    $method = mysqli_real_escape_string($connect, $_POST['method']);
    $amount = floatval($_POST['amount']);
    $max_qty = (int) $_POST['max_qty']; // Total quantity from cart
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $user_id = (int) $_POST['user_id'];

    if (!is_dir('payment')) {
        mkdir('payment', 0755, true);
    }

    $file = $_FILES['payment_image'];
    $fileName = basename($file['name']);
    $targetDir = 'payment/';
    $targetFilePath = $targetDir . uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
    $uploadOk = move_uploaded_file($file['tmp_name'], $targetFilePath);

    if ($uploadOk) {
        $order_items = [];
        $cart_items_sql = "SELECT c.plant_id, c.quantity, p.price, p.stock_qty 
                           FROM cart c 
                           JOIN plants p ON c.plant_id = p.plant_id 
                           WHERE c.user_id = $user_id";
        $cart_items_result = mysqli_query($connect, $cart_items_sql);

        while ($item = mysqli_fetch_assoc($cart_items_result)) {
            $order_items[] = $item;
        }

        if ($amount > 0 && $max_qty > 0) {
            mysqli_query($connect, "INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $amount)");
            $order_id = mysqli_insert_id($connect);

            foreach ($order_items as $item) {
                $plant_id = (int) $item['plant_id'];
                $quantity = (int) $item['quantity'];
                $price_each = (float) $item['price'];
                $current_stock = (int) $item['stock_qty'];

                if ($current_stock < $max_qty) {
                    echo "Not enough stock for plant ID $plant_id.";
                    exit;
                }

                mysqli_query(
                    $connect,
                    "INSERT INTO order_items (order_id, plant_id, quantity, price_each) 
                     VALUES ($order_id, $plant_id, $max_qty, $amount)"
                );

                // Deduct the max_qty from each plant
                mysqli_query(
                    $connect,
                    "UPDATE plants 
                     SET stock_qty = stock_qty - $max_qty 
                     WHERE plant_id = $plant_id AND stock_qty >= $max_qty"
                );
            }

            $targetFilePathEscaped = mysqli_real_escape_string($connect, $targetFilePath);
            mysqli_query(
                $connect,
                "INSERT INTO payments (order_id, user_id, method, amount, address, payment_image) 
                 VALUES ($order_id, $user_id, '$method', $amount, '$address', '$targetFilePathEscaped')"
            );

            mysqli_query($connect, "DELETE FROM cart WHERE user_id = $user_id");

            header('Location: user_dashboard.php?message=payment successfully');
            exit;
        } else {
            echo "Amount and quantity must be greater than zero.";
        }
    } else {
        echo "Failed to upload receipt.";
    }
} else {
    header('Location: cart.php');
    exit;
}
?>
