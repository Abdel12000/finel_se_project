<?php
include("common/config.php");
if (isset($_GET['id'])) {
    $employee_id = (int)$_GET['id'];
    $query = "DELETE FROM employees WHERE employee_id = $employee_id";
    mysqli_query($connect, $query);
    header("Location: equipment.php?message=Employee deleted successfully");
    exit;
}
header("Location: admin_dashboard.php?message=Invalid employee ID");
exit;
?>
