<?php
include 'common/config.php';

$plant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($plant_id > 0) {
    $res = mysqli_query($connect, "SELECT image_url FROM plants WHERE plant_id = $plant_id LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['image_url']) && file_exists($row['image_url'])) {
            @unlink($row['image_url']);
        }
    }
    mysqli_query($connect, "DELETE FROM plants WHERE plant_id = $plant_id");
}

header('Location: sell.php?message=plant deleted succesfully');
exit;
?>
