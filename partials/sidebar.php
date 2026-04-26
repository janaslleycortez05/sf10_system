<?php $role = $_SESSION['user']['role']; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<div class="sidebar">

  <a class="tab-link" href="user_dashboard.php">Dashboard</a><br>
  <a class="tab-link" href="request.php">New Request</a><br>
  <a class="tab-link" href="#">Profile</a>

  <?php if ($role === 'admin'): ?>
    <a href="admin_dashboard.php">Admin Panel</a>
  <?php endif; ?>
</div>