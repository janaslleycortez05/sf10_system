<!-- Header -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="style.css">
<?php
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
$user = $_SESSION['user'];
$initials = strtoupper(substr($user['name'], 0, 1));
?>
<nav class="navbar">
  <a href="<?= $user['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php' ?>" class="navbar-brand">
    <div class="brand-icon">SF10</div>
    <div class="brand-text">
      <span class="brand-title">SF10 Request System</span>
      <span class="brand-sub">Lazaan-Malinao Elementary</span>
    </div>
  </a>
  <div class="navbar-right">
    <div class="navbar-bell">
      <i class="bi bi-bell"></i>
      <span class="dot"></span>
    </div>
    <div class="navbar-user">
      <div class="navbar-avatar"><?= $initials ?></div>
      <span><?= htmlspecialchars($user['name']) ?></span>
    </div>
  </div>
</nav>
