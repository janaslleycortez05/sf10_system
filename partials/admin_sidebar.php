<!-- Admin User sidebar -->
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
  <div class="sidebar-section">Admin Menu</div>

  <a href="admin_dashboard.php" class="<?= $current === 'admin_dashboard.php' ? 'active' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="#" class="<?= $current === 'records.php' ? 'active' : '' ?>">
    <i class="bi bi-inbox"></i> Records
  </a>

  <a href="users.php" class="<?= $current === 'users.php' ? 'active' : '' ?>">
    <i class="bi bi-people"></i> Users
  </a>

  <div class="sidebar-spacer"></div>

  <div class="sidebar-section">Account</div>
  <a href="logout.php" class="sidebar-logout">
    <i class="bi bi-box-arrow-left"></i> Logout
  </a>
</aside>
