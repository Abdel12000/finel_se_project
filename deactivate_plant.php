<?php
session_start();

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

include 'common/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plant_id'])) {
    $plant_id = (int) $_POST['plant_id'];

    $query = "UPDATE plants SET status = 'not_active' WHERE plant_id = $plant_id";
    if (mysqli_query($connect, $query)) {
        header("Location: plants.php?message=" . urlencode("Plant deactivated successfully."));
        exit;
    } else {
        header("Location: index.php?message=" . urlencode("Error deactivating plant."));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
