<?php
session_start();
include("common/config.php");

if (!isset($_SESSION['user_email'])) {
	echo json_encode(['success' => false, 'message' => 'Not logged in']);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plant_id'])) {
	$user_email = mysqli_real_escape_string($connect, $_SESSION['user_email']);
	$plant_id = (int)$_POST['plant_id'];

	$user_result = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email'");
	if ($user_result && mysqli_num_rows($user_result) > 0) {
		$user_data = mysqli_fetch_assoc($user_result);
		$user_id = $user_data['user_id'];

		$insert = mysqli_query($connect, "INSERT INTO cart (user_id, plant_id, quantity) VALUES ('$user_id', '$plant_id', 1)");
		if ($insert) {
			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Insert failed']);
		}
	} else {
		echo json_encode(['success' => false, 'message' => 'User not found']);
	}
}
?>
