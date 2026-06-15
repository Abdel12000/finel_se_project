$user_name = "Unknown User";
$user_query = mysqli_query($connect, "SELECT username FROM users WHERE user_id = $user_id LIMIT 1");
if ($user_query && mysqli_num_rows($user_query) > 0) {
    $user_data = mysqli_fetch_assoc($user_query);
    $user_name = $user_data['username'];
    
}
<?php  echo $user_name;?>