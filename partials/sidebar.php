<!-- General User sidebar -->
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
  <div class="sidebar-section">Menu</div>

  <a href="user_dashboard.php" class="<?= $current === 'user_dashboard.php' ? 'active' : '' ?>">
    <i class="bi bi-grid-1x2"></i> Dashboard
  </a>

  <a href="request.php" class="<?= $current === 'request.php' ? 'active' : '' ?>">
    <i class="bi bi-plus-circle"></i> New Request
  </a>

  <a href="profile.php" class="<?= $current === 'profile.php' ? 'active' : '' ?>">
    <i class="bi bi-person-circle"></i> Profile
  </a>

  <div class="sidebar-spacer"></div>

  <a href="logout.php" class="sidebar-logout">
    <i class="bi bi-box-arrow-left"></i> Logout
  </a>
</aside>
