<?php $role = $_SESSION['user']['role']; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<div class="sidebar">
  <br>
  <a class="tab-link" href="user_dashboard.php"><i class="bi bi-house-door-fill"></i>&ensp;Dashboard</a><br>
  <a class="tab-link" href="request.php"><i class="bi bi-send-plus-fill"></i>&ensp;New Request</a><br>
  <a class="tab-link" href="#"><i class="bi bi-person-circle"></i>&ensp;Profile</a>

  <?php if ($role === 'admin'): ?>
    <a href="admin_dashboard.php">Admin Panel</a>
  <?php endif; ?>
</div>