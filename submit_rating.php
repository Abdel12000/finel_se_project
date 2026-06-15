<?php
include("common/config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plant_id']) && isset($_POST['stars'])) {
    $user_email = $_SESSION['user_email'];
    $user_result = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email'");
    $user_data = mysqli_fetch_assoc($user_result);
    $user_id = $user_data['user_id'];

    $plant_id = intval($_POST['plant_id']);
    $stars = intval($_POST['stars']);

    if ($stars <= 1 || $stars >= 5) {
        echo "Invalid star rating.";
        exit;
    }

    $insert = mysqli_query($connect, "INSERT INTO plant_ratings (plant_id, user_id, stars) VALUES ('$plant_id', '$user_id', '$stars')");

    header("location:user_dashboard.php?message=rated succesfully");
}
?>
