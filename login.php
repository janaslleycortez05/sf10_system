<?php
include 'db.php';

if (isset($_SESSION['user'])) {
  header("Location: " . ($_SESSION['user']['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
  exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $conn->real_escape_string(trim($_POST['email']));
  $pass  = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    if ($pass === $user['password']) {
      $_SESSION['user'] = $user;
      header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
      exit;
    }
  }
  $error = "Invalid email or password. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-bg">
  <div class="auth-card">

    <div class="auth-logo">
      <div class="auth-logo-icon">SF10</div>
      <div>
        <div class="auth-logo-text">Lazaan-Malinao Elementary</div>
        <div class="auth-logo-sub">School Record Request System</div>
      </div>
    </div>

    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">Log in to your account to continue</p>

    <?php if ($error): ?>
    <div class="alert-error">
      <i class="bi bi-exclamation-circle-fill"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" autocomplete="on">
      <div class="form-group">
        <label class="form-label">Email address</label>
        <div class="input-icon-wrap">
          <i class="bi bi-envelope"></i>
          <input name="email" type="email" class="form-control" placeholder="Enter your email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" style="display:flex;justify-content:space-between;">
          Password
        </label>
        <div class="input-icon-wrap">
          <i class="bi bi-lock"></i>
          <input name="password" type="password" class="form-control" placeholder="Enter your password" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
        <i class="bi bi-box-arrow-in-right"></i> Log In
      </button>
    </form>

    <div class="auth-link">
      Don't have an account? <a href="register.php">Sign up here</a>
    </div>
  </div>
</div>
</body>
</html>