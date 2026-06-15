<?php
    if(isset($_GET["id"]))
    {
        $user_id = $_GET["id"];
        include("common/config.php");
        $qrs = "INSERT INTO notifications(sender_id,title,body) VALUES($user_id,'cancelation','i cancel that i will work')";
        $q = mysqli_query($connect,$qrs);
        if($q)
        {
            header("location:work_employee.php?message=cancel");
        }
    }

?>