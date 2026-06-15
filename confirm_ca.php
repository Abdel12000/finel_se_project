<?php
if (isset($_GET["id"])) {
    $user_id = $_GET["id"];
    include("common/config.php");

    $select_id = "SELECT equipment_id FROM assignments WHERE employee_id = $user_id";
    $qrs_res = mysqli_query($connect,$select_id);
    $query_result = mysqli_fetch_assoc($qrs_res);
    $equipment_id  = (int)$query_result["equipment_id"];

    $query3 = mysqli_query($connect,"SELECT name FROM equipment WHERE equipment_id = $equipment_id");
    $query3_result = mysqli_fetch_assoc($query3);
    $equipment_name = $query3_result["name"];


   $qr_insert = mysqli_query($connect,"INSERT INTO notifications (sender_id,title,body) VALUES($user_id,'confirm','$equipment_name')");
    
 

    if ($qr_insert) {
        header("Location: work_employee.php?message=confirmed&equipment=$equipment_name");
        exit;
    }
}
?>