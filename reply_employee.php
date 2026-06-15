<?php
    if(isset($_GET["user_id"]))
    {
        include("common/config.php");
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $sender_id = $_GET["user_id"];
            $title =  mysqli_real_escape_string($connect,$_POST["title"]);
        $subject =  mysqli_real_escape_string($connect,$_POST["subject"]);
         $insert_sql = "INSERT INTO notifications (sender_id, title, body) VALUES ($sender_id, '$title', '$subject')";
         $s=mysqli_query($connect,$insert_sql);
         if($s){
            echo "true";
         }
        }
        


    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="title">
        <input type="text" name="subject">
        <button>submit</button>

    </form>
</body>
</html>