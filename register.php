<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Insert user (default role = user)
  $conn->query("INSERT INTO users (name, email, password, role)
  VALUES ('$name', '$email', '$password', 'user')");

  header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card p-4 shadow" style="width: 350px;">
  <h4 class="text-center">Create Account</h4>

  <form method="POST">
    <input name="name" class="form-control my-2" placeholder="Full Name" required>
    <input name="email" class="form-control my-2" placeholder="Email" required>
    <input name="password" type="password" class="form-control my-2" placeholder="Password" required>

    <button class="btn btn-primary w-100">Register</button>
  </form>

  <p class="text-center mt-2">
    Already have an account? <a href="login.php">Login</a>
  </p>
</div>

</body>
</html>