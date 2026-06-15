<?php
include("common/config.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($connect, $_POST["name"]);
    $email = mysqli_real_escape_string($connect, $_POST["email"]);
    $phone = mysqli_real_escape_string($connect, $_POST["phone"]);
    $address = mysqli_real_escape_string($connect, $_POST["address"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm_password)) {
        $message = "Please fill in all fields.";
    } elseif ($password != $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($connect, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "An account with this email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 0;
            $status = 'Active';
            $created_at = date("Y-m-d H:i:s");
            $updated_at = $created_at;

            $insert_query = "INSERT INTO users (username, email, phone, address, password_hash, role, status, created_at, updated_at)
                             VALUES ('$name', '$email', '$phone', '$address', '$hashed_password', '$role', '$status', '$created_at', '$updated_at')";

            if (mysqli_query($connect, $insert_query)) {
                header("location:user_dashboard.php");
                exit;
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Account</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #ffffff;
    }
    .form-container {
      max-width: 400px;
      margin: 100px auto;
    }
    .form-control {
      border-radius: 0.5rem;
      background-color: #f2f2f2;
      border: none;
      padding: 0.75rem;
    }
    .form-label {
      text-transform: capitalize;
      font-size: 0.85rem;
      color: #888;
      margin-bottom: 0.25rem;
    }
    .btn-custom {
      border-radius: 0.5rem;
      font-weight: bold;
    }
    .heading {
      font-weight: 700;
    }
    .subheading {
      color: #777;
      font-weight: 500;
      margin-bottom: 2rem;
    }
    .login-link {
      margin-top: 1rem;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <div class="container form-container text-center">
    <h2 class="heading">Create Account</h2>
    <p class="subheading">Sign up for your new account.</p>

    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3 text-start">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control" id="name" placeholder="Jane Smith" />
      </div>

      <div class="mb-3 text-start">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="jane@framer.com" />
      </div>

      <div class="mb-3 text-start">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" id="phone" placeholder="+961123456" />
      </div>

      <div class="mb-3 text-start">
        <label for="address" class="form-label">Address</label>
        <input type="text" name="address" class="form-control" id="address" placeholder="Beirut, Lebanon" />
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="password" />
      </div>

      <div class="mb-4 text-start">
        <label for="confirm-password" class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" id="confirm-password" placeholder="confirm password" />
      </div>

      <button type="submit" class="btn btn-dark w-100 btn-custom">Create Account</button>

      <div class="login-link text-center">
        <span>Already have an account? <a href="login.php" class="text-black">Login</a></span>
      </div>
    </form>
  </div>
</body>
</html>
