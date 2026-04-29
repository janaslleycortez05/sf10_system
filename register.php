<?php
include 'db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = $conn->real_escape_string(trim($_POST['name']));
  $email    = $conn->real_escape_string(trim($_POST['email']));
  $password = $_POST['password'];
  $confirm  = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $error = "Passwords do not match.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } else {
    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
      $error = "An account with that email already exists.";
    } else {
      $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
      $stmt->bind_param("sss", $name, $email, $password);
      $stmt->execute();
      header("Location: login.php?registered=1");
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-bg">
  <div class="auth-card" style="max-width:460px;">

    <div class="auth-logo">
      <div class="auth-logo-icon">SF10</div>
      <div>
        <div class="auth-logo-text">Lazaan-Malinao Elementary</div>
        <div class="auth-logo-sub">School Record Request System</div>
      </div>
    </div>

    <h2 class="auth-title">Create an Account</h2>
    <p class="auth-subtitle">Register to request your school records</p>

    <?php if ($error): ?>
    <div class="alert-error">
      <i class="bi bi-exclamation-circle-fill"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" autocomplete="on">
      <div class="form-group">
        <label class="form-label">Full Name <span>*</span></label>
        <div class="input-icon-wrap">
          <i class="bi bi-person"></i>
          <input name="name" type="text" class="form-control" placeholder="Enter your full name"
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Email Address <span>*</span></label>
        <div class="input-icon-wrap">
          <i class="bi bi-envelope"></i>
          <input name="email" type="email" class="form-control" placeholder="Enter your email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Password <span>*</span></label>
          <div class="input-icon-wrap">
            <i class="bi bi-lock"></i>
            <input name="password" type="password" class="form-control" placeholder="Create a password" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirm Password <span>*</span></label>
          <div class="input-icon-wrap">
            <i class="bi bi-lock-fill"></i>
            <input name="confirm_password" type="password" class="form-control" placeholder="Repeat password" required>
          </div>
        </div>
      </div>
      <p class="form-hint" style="margin-top:-10px;margin-bottom:16px;">Minimum 6 characters</p>

      <button type="submit" class="btn btn-primary btn-block btn-lg">
        <i class="bi bi-person-plus"></i> Create Account
      </button>
    </form>

    <div class="auth-link">
      Already have an account? <a href="login.php">Log in here</a>
    </div>
  </div>
</div>
</body>
</html>