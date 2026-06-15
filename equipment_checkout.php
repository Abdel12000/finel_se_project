<?php
session_start();
include 'common/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['user_id'], $_POST['payment_method'], $_POST['payment_number'], $_FILES['payment_image'], $_POST['amount'])
) {
    $user_id = (int)$_POST['user_id'];
    $payment_method = mysqli_real_escape_string($connect, $_POST['payment_method']);
    $payment_number = mysqli_real_escape_string($connect, $_POST['payment_number']);
    $amount = floatval($_POST['amount']);

    if (!is_dir('payment')) {
        mkdir('payment', 0755, true);
    }

    $file = $_FILES['payment_image'];
    $fileName = basename($file['name']);
    $targetDir = 'payment/';
    $targetFilePath = $targetDir . uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        $targetFilePathEscaped = mysqli_real_escape_string($connect, $targetFilePath);

        $equipments_result = mysqli_query($connect, "SELECT equipment_id FROM equipment_bookings WHERE user_id = $user_id");

        if (!$equipments_result) {
            echo "Database error: " . mysqli_error($connect);
            exit;
        }

        if (mysqli_num_rows($equipments_result) > 0) {
            while ($row = mysqli_fetch_assoc($equipments_result)) {
                $equipment_id = (int)$row['equipment_id'];

                $insert_payment_sql = "INSERT INTO equipment_payments (equipment_id, payment_method, payment_number, payment_image, amount) 
                                       VALUES ($equipment_id, '$payment_method', '$payment_number', '$targetFilePathEscaped', $amount)";
                mysqli_query($connect, $insert_payment_sql);

                mysqli_query($connect, "DELETE FROM equipment_bookings WHERE equipment_id = $equipment_id");
                mysqli_query($connect, "DELETE FROM equipment WHERE equipment_id = $equipment_id");
            }
            header("Location: user_dashboard.php?message=payment+successful+and+equipment+deleted");
            exit;
        } else {
            echo "No equipment bookings found for this user.";
        }
    } else {
        echo "Failed to upload payment receipt.";
    }
} else {
    header('Location: equipment_checkout.php');
    exit;
}
?>
