<?php
session_start();
include 'common/config.php';

$user_email = $_SESSION['user_email'] ?? '';

if (!$user_email) {
	header("Location: login.php");
	exit;
}

$email_safe = mysqli_real_escape_string($connect, $user_email);
$user_query = "SELECT user_id, status FROM users WHERE email = '$email_safe' LIMIT 1";
$user_result = mysqli_query($connect, $user_query);

if (!$user_result || mysqli_num_rows($user_result) === 0) {
	echo "User not found.";
	exit;
}

$user_data = mysqli_fetch_assoc($user_result);
$user_id = (int) $user_data['user_id'];
$status = $user_data['status'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'inactive') {
	$title = mysqli_real_escape_string($connect, $_POST['title'] ?? '');
	$subject = mysqli_real_escape_string($connect, $_POST['subject'] ?? '');

	if ($title && $subject) {
		$check_sql = "SELECT COUNT(*) AS count FROM notifications WHERE sender_id = $user_id AND title = '$title' AND body = '$subject'";
		$check_result = mysqli_query($connect, $check_sql);
		$count_row = mysqli_fetch_assoc($check_result);

		if ($count_row['count'] > 0) {
			$message = "You have already sent this activation request.";
		} else {
			$insert_sql = "INSERT INTO notifications (sender_id, title, body) VALUES ($user_id, '$title', '$subject')";
			if (mysqli_query($connect, $insert_sql)) {
				$message = "Notification sent successfully. Your account will be activated within 24 hours.";
			} else {
				$message = "Failed to send notification.";
			}
		}
	} else {
		$message = "Please fill in all fields.";
	}
}

if ($status === 'inactive') {
	echo '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Account Inactive</h4>
                    </div>
                    <div class="card-body">
                        ' . ($message ? '<div class="alert alert-info">' . $message . '</div>' : '') . '
                        <form action="" method="POST">
                            <input type="hidden" name="user_id" value="' . $user_id . '">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <textarea name="subject" id="subject" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Activation Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>';
	exit;
}
?>
<?php
$user_email = $_SESSION["user_email"];
$user_query = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);
$user_id = $user['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['read_id'])) {
	$read_id = intval($_POST['read_id']);
	$update_query = "UPDATE feedback SET is_read = 'Read' WHERE feedback_id = $read_id AND user_id = $user_id";
	mysqli_query($connect, $update_query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
	$delete_id = intval($_POST['delete_id']);
	$delete_query = "DELETE FROM feedback WHERE feedback_id = $delete_id AND user_id = $user_id";
	mysqli_query($connect, $delete_query);
}

$feedback_query = "SELECT feedback_id, message, admin_response, is_read FROM feedback WHERE user_id = $user_id ORDER BY feedback_id DESC";
$feedback_result = mysqli_query($connect, $feedback_query);
?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
	<title>Plantly - Free Plant Selling Website Template</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="author" content="templatesjungle">
	<meta name="keywords" content="plant shop">
	<meta name="description" content="Free Plant Selling Website Template">

	<link rel="stylesheet" type="text/css" href="user/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="user/icomoon/icomoon.css">
	<link rel="stylesheet" type="text/css" href="user/css/vendor.css">

	<link rel="stylesheet" type="text/css" href="user/style.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<style>
		#notification-popup {
			position: fixed;
			bottom: 20px;
			right: 20px;
			max-width: 300px;
			z-index: 1050;
			border-radius: 10px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			background-color: #25D366;
			color: white;
			padding: 15px;
			display: none;
			cursor: pointer;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		#notification-popup .header {
			font-weight: bold;
			font-size: 16px;
			margin-bottom: 8px;
		}

		#notification-popup .close-btn {
			float: right;
			cursor: pointer;
			font-size: 18px;
		}

		#notification-popup .content {
			font-size: 14px;
			line-height: 1.3;
		}
	</style>
</head>

</head>

<body>

	<?php include("user/header.php"); ?>

	<section id="intro" class="scrollspy-section swiper intro-swiper">
		<div class="swiper-wrapper">

			<div class="swiper-slide">
				<div class="banner-content">
					<h2 class="banner-title">Perfect plants on sale for your <span class="text-pri">interiors</span>.
					</h2>
					<div class="btn-wrap">
						<a href="#" class="btn btn-black btn-xlarge btn-rounded">Shop collection
							<i class="icon icon-arrow-right"></i>
						</a>
					</div>
				</div><!--banner-content-->
				<img src="user/images/main-banner.png" alt="banner">
			</div><!--swiper-slide-->

			<div class="swiper-slide">
				<div class="banner-content">
					<h2 class="banner-title">Plants Create your place <span class="text-pri">special</span>.</h2>
					<div class="btn-wrap">
						<a href="#" class="btn btn-black btn-xlarge btn-rounded">Shop collection
							<i class="icon icon-arrow-right"></i>
						</a>
					</div>
				</div><!--banner-content-->
				<img src="user/images/main-banner2.png" alt="banner">
			</div><!--swiper-slide-->

			<div class="swiper-slide">
				<div class="banner-content">
					<h2 class="banner-title">Plants Create fresh and <span class="text-pri">comfort air.</span>.</h2>
					<div class="btn-wrap">
						<a href="#" class="btn btn-black btn-xlarge btn-rounded">Shop collection
							<i class="icon icon-arrow-right"></i>
						</a>
					</div>
				</div><!--banner-content-->
				<img src="user/images/main-banner.png" alt="banner">
			</div><!--swiper-slide-->

			<div class="swiper-slide">
				<div class="banner-content">
					<h2 class="banner-title">Plants Create your place <span class="text-pri">special</span>.</h2>
					<div class="btn-wrap">
						<a href="#" class="btn btn-black btn-xlarge btn-rounded">Shop collection
							<i class="icon icon-arrow-right"></i>
						</a>
					</div>
				</div><!--banner-content-->
				<img src="user/images/main-banner2.png" alt="banner">
			</div><!--swiper-slide-->

		</div><!--slider-->

		<div class="swiper-pagination"></div>
	</section>

	<div id="notification-popup">
		<div class="header">
			Notification
			<span class="close-btn"
				onclick="document.getElementById('notification-popup').style.display='none'">&times;</span>
		</div>
		<div class="content" id="popup-content"></div>
	</div>
	<script>
		const notifications = <?php
		$notes = [];
		mysqli_data_seek($feedback_result, 0);
		while ($row = mysqli_fetch_assoc($feedback_result)) {
			if ($row['is_read'] !== 'Read') {  // Only unread notifications
				$notes[] = [
					"id" => $row['feedback_id'],
					"title" => $row['message'],
					"body" => $row['admin_response'],
				];
			}
		}
		echo json_encode($notes);
		?>;

		const popup = document.getElementById('notification-popup');
		const content = document.getElementById('popup-content');

		function showNotification(index = 0) {
			if (!notifications.length) {
				popup.style.display = 'none';
				return;
			}
			if (index >= notifications.length) {
				popup.style.display = 'none';
				return;
			}
			const note = notifications[index];
			content.innerHTML = `<strong>${note.title}</strong><br>${note.body}`;
			popup.style.display = 'block';

			popup.onclick = () => showNotification(index + 1);
		}

		showNotification();
	</script>



	<section id="about" class="scrollspy-section margin-xlarge">
		<div class="container">
			<div class="row">
				<div class="col-md-6 video-content">
					<div class="video-player">
						<a id="video-item" href="javascript:void(0)">
							<i class="icon icon-youtube-player"></i>
							<img src="user/images/video-item.png" alt="video" class="video-image">
						</a>
					</div>
				</div><!--video-content-->

				<div class="col-md-5 description">
					<div class="section-header">
						<h2 class="section-title">Our journey</h2>
					</div>

					<p>Quis eleifend orci nunc, blandit massa, vitae. Dui nulla augue in id enim non. Venenatis aenean
						suscipit facilisis amet. Pellentesque nullam mi vitae neque ipsum. Quis in vitae est eu pulvinar
						sed. Netus lorem sit turpis tristique pharetra sit. Tortor ornare libero semper cursus. Mollis
						erat augue egestas laoreet est auctor.</p>
					<p>Dui nulla augue in id enim non. Venenatis aenean suscipit facilisis amet. Pellentesque nullam mi
						vitae neque ipsum. Sem magna ut pharetra vitae duis eu senectus sem risus. Morbi non, semper
						vestibulum euismod accumsan augue.</p>

					<div class="btn-wrap">
						<a href="#" class="btn btn-black btn-xlarge btn-rounded">Read More
							<i class="icon icon-arrow-right"></i></a>
					</div>

				</div>
			</div>

		</div>
	</section>



	<section id="why-us" class="scrollspy-section bg-accent padding-xlarge">
		<div class="container">
			<div class="row">
				<div class="col-md-6 left-column">
					<div class="section-header">
						<h2 class="section-title">Why shop with us?</h2>
					</div>
					<div class="expertize">
						<p>Dui nulla augue in id enim non. Venenatis aenean suscipit facilisis amet. Pellentesque nullam
							mi vitae neque ipsum. Sem magna ut pharetra vitae duis eu senectus sem risus. Morbi non,
							semper vestibulum euismod accumsan augue.
						</p>
						<ul>
							<li>
								<span class="list-number">1.</span>
								<div class="list-title">
									<h4>Socculents</h4>
									<p>At in proin consequat ut cursus venenatis sapien.</p>
								</div>
							</li>
							<li>
								<span class="list-number">2.</span>
								<div class="list-title">
									<h4>Air purifiers</h4>
									<p>At in proin consequat ut cursus venenatis sapien.</p>
								</div>
							</li>
							<li>
								<span class="list-number">3.</span>
								<div class="list-title">
									<h4>Decorative</h4>
									<p>At in proin consequat ut cursus venenatis sapien.</p>
								</div>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-md-6 right-column">
					<figure class="single-image-holder">
						<img src="user/images/plant-item15.png" alt="plant-image">
					</figure>
				</div>
			</div>
		</div>
	</section>
	<section id="popular-items" class="herb-items scrollspy-section margin-xlarge">
		<div class="container">
			<div class="section-header text-center mb-5">
				<h2 class="section-title">Popular Items</h2>
				<?php if (isset($_GET["message"])) { ?>
					<div id="msgBox"
						style="padding:10px; background-color:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:5px; margin-bottom:15px;">
						<?= htmlspecialchars($_GET["message"]) ?>
					</div>
					<script>
						setTimeout(function () {
							var msg = document.getElementById('msgBox');
							if (msg) {
								msg.style.display = 'none';
							}
						}, 4000);
					</script>
				<?php } ?>
			</div>

			<div class="d-flex justify-content-center flex-wrap gap-3 mb-5" id="category-buttons">
				<?php
				include("common/config.php");
				if (!isset($user_id)) {
					session_start();
					$user_email = $_SESSION['user_email'];
					$user_result = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email'");
					$user_data = mysqli_fetch_assoc($user_result);
					$user_id = $user_data['user_id'];
				}
				$categories_query = mysqli_query($connect, "SELECT category_id, name FROM categories WHERE user_id != $user_id OR user_id = 0");
				$first = true;
				while ($cat = mysqli_fetch_assoc($categories_query)) {
					$activeClass = $first ? "active" : "";
					echo "<button class='btn btn-outline-success $activeClass' onclick=\"filterPlants('cat-{$cat['category_id']}')\">{$cat['name']}</button>";
					$first = false;
				}
				?>
			</div>

			<div class="row" id="plant-container">
				<?php
				include("common/config.php");
				$plants_query = mysqli_query($connect, "
	SELECT 
		p.plant_id,
		p.name,
		p.price,
		p.image_url,
		c.category_id,
		IFNULL(AVG(r.stars), 0) AS avg_rating,
		COUNT(r.stars) AS total_ratings
	FROM plants p
	INNER JOIN categories c ON p.category_id = c.category_id
	LEFT JOIN plant_ratings r ON p.plant_id = r.plant_id
	WHERE (p.user_id != $user_id OR p.user_id = 0)
	AND p.status = 'active'
	GROUP BY p.plant_id
	ORDER BY avg_rating DESC, total_ratings DESC
	LIMIT 10
");

				while ($plant = mysqli_fetch_assoc($plants_query)) {
					$catClass = "cat-" . $plant['category_id'];
					$image = htmlspecialchars($plant['image_url']);
					$name = htmlspecialchars($plant['name']);
					$price = number_format($plant['price'], 2);
					$plant_id = $plant['plant_id'];
					echo "
				<div class='col-lg-3 col-md-4 col-sm-6 mb-4 plant-item $catClass'>
					<div class='product-card position-relative cart-hover'>
						<div class='product-image'>
							<img src='$image' alt='plant' class='img-fluid'>
							<button class='rate-btn' data-plantid='$plant_id' title='Rate this plant'>&#9734;</button>
						</div>
						<div class='product-info p-3 text-center'>
							<h5 class='product-name'>$name</h5>
							<p class='product-price'>\$ $price</p>
							<button type='button' class='btn btn-success btn-sm w-100 mt-2 add-to-cart-btn' data-plantid='$plant_id'>Add to Cart</button>
						</div>
						<form method='POST' class='rating-box d-none' id='rating-popup-$plant_id'>
							<input type='hidden' name='plant_id' value='$plant_id'>
							<input type='hidden' name='stars' class='selected-stars' value=''>
							<div class='stars'>
								<span class='star' data-value='1'>&#9733;</span>
								<span class='star' data-value='2'>&#9733;</span>
								<span class='star' data-value='3'>&#9733;</span>
								<span class='star' data-value='4'>&#9733;</span>
								<span class='star' data-value='5'>&#9733;</span>
							</div>
							<div class='mt-2 text-center'>
								<button type='submit' name='submit_rating' class='btn btn-sm btn-primary'>Submit</button>
								<button type='button' class='btn btn-sm btn-outline-secondary cancel-rating' data-plantid='$plant_id'>Cancel</button>
							</div>
						</form>
					</div>
				</div>";
				}
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'], $_POST['plant_id'], $_POST['stars'])) {
					include("common/config.php");
					$user_email = $_SESSION['user_email'] ?? '';
					$user_result = mysqli_query($connect, "SELECT user_id FROM users WHERE email = '$user_email'");
					if ($user_result && mysqli_num_rows($user_result) > 0) {
						$user_data = mysqli_fetch_assoc($user_result);
						$user_id = $user_data['user_id'];
						$plant_id = intval($_POST['plant_id']);
						$stars = intval($_POST['stars']);
						if ($stars >= 1 && $stars <= 5) {
							mysqli_query($connect, "INSERT INTO plant_ratings (plant_id, user_id, stars) VALUES ('$plant_id', '$user_id', '$stars')");
							echo "<script>window.location.href='user_dashboard.php?message=rated+successfully';</script>";
						} else {
							echo "<script>alert('Invalid star rating.');</script>";
						}
					} else {
						echo "<script>alert('User not found.');</script>";
					}
				}
				?>
			</div>
		</div>
	</section>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const buttons = document.querySelectorAll('.add-to-cart-btn');
			buttons.forEach(button => {
				button.addEventListener('click', function () {
					const plantId = this.getAttribute('data-plantid');
					fetch('add_to_cart.php', {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: 'plant_id=' + encodeURIComponent(plantId)
					})
						.then(response => response.json())
						.then(data => {
							if (data.success) {
								const msg = document.createElement('div');
								msg.innerText = 'Plant added to cart successfully!';
								msg.style.position = 'fixed';
								msg.style.top = '20px';
								msg.style.right = '20px';
								msg.style.background = '#4CAF50';
								msg.style.color = 'white';
								msg.style.padding = '10px 20px';
								msg.style.borderRadius = '5px';
								msg.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
								msg.style.zIndex = '9999';
								msg.style.fontFamily = 'Arial, sans-serif';
								msg.style.fontSize = '14px';

								document.body.appendChild(msg);

								setTimeout(() => {
									msg.remove();
								}, 3000);

							} else {
								alert('Error: ' + data.message);
							}

						})
						.catch(() => {
							alert('AJAX error.');
						});
				});
			});
		});
	</script>

	<style>
		.product-card {
			background: #fff;
			border-radius: 12px;
			box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
			overflow: hidden;
			transition: transform 0.3s ease;
		}

		.cart-hover:hover {
			transform: scale(1.03);
			box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
		}

		.product-image {
			position: relative;
			overflow: hidden;
			height: 200px;
		}

		.product-image img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			transition: transform 0.3s ease;
		}

		.cart-hover:hover .product-image img {
			transform: scale(1.1);
		}

		.rate-btn {
			position: absolute;
			top: 10px;
			right: 10px;
			background: white;
			border: none;
			color: #f39c12;
			font-size: 20px;
			border-radius: 50%;
			padding: 4px 7px;
			display: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
			cursor: pointer;
		}

		.cart-hover:hover .rate-btn {
			display: block;
		}

		.product-info h5 {
			font-size: 1.1rem;
			font-weight: 600;
		}

		.product-price {
			color: #28a745;
			font-size: 1rem;
			font-weight: 700;
		}

		.rating-box {
			position: absolute;
			top: 40%;
			left: 50%;
			transform: translate(-50%, -50%);
			background: #fff;
			padding: 16px;
			border-radius: 12px;
			box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
			z-index: 20;
		}

		.stars .star {
			font-size: 24px;
			color: #ccc;
			margin: 0 4px;
			cursor: pointer;
		}

		.stars .star:hover,
		.stars .star.selected {
			color: #f1c40f;
		}

		.btn-outline-success.active {
			background-color: #28a745;
			color: white;
			border-color: #28a745;
		}
	</style>
	<script>
		const popup = document.getElementById('notification-popup');
		const content = document.getElementById('popup-content');
		let notifications = [];
		let currentIndex = 0;

		function fetchNotifications() {
			fetch('get_notification.php')
				.then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						notifications = data.notifications;

						// Check if there are any unread notifications
						const unread = notifications.filter(n => !n.is_read);
						if (unread.length > 0) {
							currentIndex = 0;
							showNotification(currentIndex);
						}
					}
				})
				.catch(err => console.error('Error fetching notifications:', err));
		}

		function showNotification(index) {
			if (index >= notifications.length) {
				popup.style.display = 'none';
				return;
			}
			if (notifications[index].is_read) {
				showNotification(index + 1);
				return;
			}
			content.innerHTML = `<strong>${notifications[index].title}</strong><br>${notifications[index].body}`;
			popup.style.display = 'block';
		}

		popup.onclick = function () {
			currentIndex++;
			showNotification(currentIndex);
		};

		// Fetch notifications every 5 seconds
		setInterval(fetchNotifications, 5000);

		// Initial fetch
		fetchNotifications();

	</script>
	<script src="user/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			document.querySelectorAll(".rate-btn").forEach(button => {
				button.addEventListener("click", function () {
					const plantId = this.getAttribute("data-plantid");
					document.getElementById(`rating-popup-${plantId}`).classList.remove("d-none");
				});
			});

			document.querySelectorAll(".cancel-rating").forEach(button => {
				button.addEventListener("click", function () {
					const plantId = this.getAttribute("data-plantid");
					document.getElementById(`rating-popup-${plantId}`).classList.add("d-none");
				});
			});

			document.querySelectorAll(".rating-box .star").forEach(star => {
				star.addEventListener("click", function () {
					const value = this.getAttribute("data-value");
					const parent = this.closest(".rating-box");
					const starsInput = parent.querySelector(".selected-stars");
					starsInput.value = value;

					// Optional: highlight selected stars visually
					parent.querySelectorAll(".star").forEach(s => s.classList.remove("selected"));
					this.classList.add("selected");
					let prev = this.previousElementSibling;
					while (prev) {
						prev.classList.add("selected");
						prev = prev.previousElementSibling;
					}
				});
			});
		});
	</script>




	<script>
		function filterPlants(category) {
			const buttons = document.querySelectorAll('.btn-outline-success');
			buttons.forEach(btn => btn.classList.remove('active'));
			const activeBtn = [...buttons].find(btn => btn.getAttribute('onclick').includes(category));
			if (activeBtn) activeBtn.classList.add('active');
			const items = document.querySelectorAll('.plant-item');
			items.forEach(item => {
				item.style.display = item.classList.contains(category) ? 'block' : 'none';
			});
		}

		window.addEventListener('DOMContentLoaded', () => {
			const firstBtn = document.querySelector('#category-buttons .btn-outline-success.active');
			if (firstBtn) {
				const match = firstBtn.getAttribute('onclick').match(/filterPlants\('(.+?)'\)/);
				if (match && match[1]) filterPlants(match[1]);
			}

			document.querySelectorAll('.rate-btn').forEach(btn => {
				btn.addEventListener('click', e => {
					const plantId = btn.dataset.plantid;
					const popup = document.getElementById('rating-popup-' + plantId);
					popup.classList.remove('d-none');
				});
			});

			document.querySelectorAll('.cancel-rating').forEach(btn => {
				btn.addEventListener('click', e => {
					const plantId = btn.dataset.plantid;
					const popup = document.getElementById('rating-popup-' + plantId);
					popup.classList.add('d-none');
				});
			});

			document.querySelectorAll('.stars .star').forEach(star => {
				star.addEventListener('click', () => {
					const allStars = star.parentElement.querySelectorAll('.star');
					allStars.forEach(s => s.classList.remove('selected'));
					let selected = false;
					allStars.forEach(s => {
						if (!selected) s.classList.add('selected');
						if (s === star) selected = true;
					});
				});
			});

			document.querySelectorAll('.submit-rating').forEach(btn => {
				btn.addEventListener('click', () => {
					const plantId = btn.dataset.plantid;
					const popup = document.getElementById('rating-popup-' + plantId);
					const selected = popup.querySelectorAll('.star.selected').length;
					popup.classList.add('d-none');
					alert("Rated " + selected + " star(s) for plant ID: " + plantId);
				});
			});
		});
	</script>







	<section id="services">
		<div class="container">
			<div class="row">

				<div class="col-md-3">
					<div class="services-item">
						<i class="icon icon-shopping-cart"></i>
						<div class="services-content">
							<div class="services-title">Free Shipping</div>
							<p>Capped at $319 per order</p>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="services-item">
						<i class="icon icon-secure"></i>
						<div class="services-content">
							<div class="services-title">Safe Payment</div>
							<p>With our payment gateway</p>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="services-item">
						<i class="icon icon-guarantee"></i>
						<div class="services-content">
							<div class="services-title">Quality Guarantee</div>
							<p>Fresh & Super item available</p>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="services-item">
						<i class="icon icon-price-tag"></i>
						<div class="services-content">
							<div class="services-title">Big Offers</div>
							<p>We give best offers surely</p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>

	<footer id="footer" class="bg-accent padding-xlarge">
		<div class="container">
			<div class="row">

				<div class="col-md-3 footer-intro">
					<div class="footer-menu">
						<img src="user/images/logo.png" alt="logo" class="footer-logo">
						<p>Sem magna ut pharetra vitae duis eu senectus sem risus. Morbi non, semper vestibulum euismod
							accumsan augue.</p>

						<div class="form-content">
							<form>
								<input type="text" name="email" placeholder="enter your email address">
								<button class="btn btn-black">Subscribe</button>
							</form>
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="footer-menu">
						<h5>Company</h5>
						<ul class="menu-list">
							<li class="menu-item">
								<a href="#">About</a>
							</li>
							<li class="menu-item">
								<a href="#">Our Plantations</a>
							</li>
							<li class="menu-item">
								<a href="#">Our vision</a>
							</li>
							<li class="menu-item">
								<a href="#">Installations</a>
							</li>
							<li class="menu-item">
								<a href="#">Refer a friend</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-md-2">
					<div class="footer-menu">
						<h5>Support</h5>
						<ul class="menu-list">
							<li class="menu-item">
								<a href="#">Customer FAQs</a>
							</li>
							<li class="menu-item">
								<a href="#">Shipping & Returns</a>
							</li>
							<li class="menu-item">
								<a href="#">Contact Us</a>
							</li>
							<li class="menu-item">
								<a href="#">Plant Care Tips</a>
							</li>
							<li class="menu-item">
								<a href="#">Privacy Policy</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-md-3">
					<div class="footer-menu">
						<h5>Contact Us</h5>
						<ul class="menu-list">
							<li class="menu-item">
								Street Avenue 487, New York, USA
							</li>
							<li class="menu-item">
								+333 346 364 366
							</li>
							<li class="menu-item">
								<a href="#" class="mail-id">info@templatesjungle.com</a>
							</li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</footer>



	<script src="user/js/jquery-3.2.1.min.js"></script>
	<script src="user/js/bootstrap.bundle.min.js"></script>
	<script src="user/js/plugins.js"></script>

	<script src="user/js/ui-easing.js"></script>
	<script src="user/js/videopopup.js"></script>
	<script src="user/js/script.js"></script>


	<script>
		var scrollToTopBtn = document.getElementById("scroll-up");
		var rootElement = document.documentElement;

		function scrollToTop() {
			// Scroll to top logic
			rootElement.scrollTo({
				top: 0,
				behavior: "smooth"
			});
		}
		scrollToTopBtn.addEventListener("click", scrollToTop);
	</script>

</body>

</html>