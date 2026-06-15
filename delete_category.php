<?php
include("common/config.php");

if (!isset($_GET['id'])) {
    echo "Category ID is missing.";
    exit;
}

$category_id = (int) $_GET['id'];

$deleteQuery = "DELETE FROM categories WHERE category_id = $category_id";

if (mysqli_query($connect, $deleteQuery)) {
    header("Location: plants.php?message=Category deleted successfully");
    exit;
} else {
    echo "Error deleting category.";
}
?>
