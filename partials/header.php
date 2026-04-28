<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<nav class="navbar navbar-dark px-3 fixed-top">
  <span class="navbar-brand">SF10 Request System</span>
  <div>
    <?php if(isset($_SESSION['user'])): ?>
      <span class="text-white me-3">Hi <?=$_SESSION['user']['name']?>!</span>
      <a href="logout.php" class="btn btn-primary btn-sm">Logout</a>
    <?php endif; ?>
  </div>
</nav>