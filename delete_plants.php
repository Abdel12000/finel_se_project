<?php
include 'common/config.php';

$plant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($plant_id > 0) {
    // Delete order_items that reference this plant_id
    mysqli_query($connect, "DELETE FROM order_items WHERE plant_id = $plant_id");

    // Now delete the image file if exists
    $res = mysqli_query($connect, "SELECT image_url FROM plants WHERE plant_id = $plant_id LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['image_url']) && file_exists($row['image_url'])) {
            @unlink($row['image_url']);
        }
    }

    // Delete plant
    mysqli_query($connect, "DELETE FROM plants WHERE plant_id = $plant_id");
}

header('Location: plants.php?message=plant deleted successfully');
exit;
?>
