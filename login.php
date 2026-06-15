<?php
session_start();
include("common/config.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = mysqli_real_escape_string($connect, $_POST["email"]);
  $password = $_POST["password"];

  if (empty($email) || empty($password)) {
    $message = "Please fill in all fields.";
  } else {
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_assoc($result);
      if (password_verify($password, $row["password_hash"])) {
        if ($row["role"] == 1) {
          $_SESSION["admin_email"] = $email;
          header("Location: index.php");
          exit();
        } elseif ($row["role"] == 2) {
          $_SESSION["user_email"] = $email;
          header("Location: employee_dashboard.php");
          exit();
        } else {
          $_SESSION["user_email"] = $email;
          header("Location: user_dashboard.php");
          exit();
        }
      } else {
        $message = "Incorrect password.";
      }
    } else {
      $message = "Email not found.";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-white">
  <div class="w-100" style="max-width: 400px;">
    <h3 class="text-center fw-bold">Login to your account</h3>
    <p class="text-center text-secondary">Please enter your email and password.</p>

    <?php if (!empty($message)): ?>
      <div class="alert alert-danger text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control bg-light" placeholder="Email">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control bg-light" placeholder="Password">
      </div>
      <button type="submit" class="btn btn-dark w-100 fw-bold">Login</button>
      <div class="text-center mt-3">
        <span>Don't have an account? </span>
        <a href="register.php" class="text-black">Register</a>
      </div>
    </form>
  </div>
</body>

</html>