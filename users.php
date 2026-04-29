<?php
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php"); exit;
}

$message = '';

// ADD USER
if (isset($_POST['add'])) {
  $name     = $conn->real_escape_string(trim($_POST['name']));
  $email    = $conn->real_escape_string(trim($_POST['email']));
  $password = $conn->real_escape_string($_POST['password']);
  $role     = $_POST['role'] === 'admin' ? 'admin' : 'user';

  $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name','$email','$password','$role')");
  $message = "User added successfully.";
}

// DELETE USER
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id !== (int)$_SESSION['user']['id']) {
    $conn->query("DELETE FROM users WHERE id = $id");
    $message = "User deleted.";
  } else {
    $message = "You cannot delete your own account.";
  }
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
$users  = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/admin_sidebar.php'; ?>

  <main class="main">
    <div class="page-header">
      <h2>User Management</h2>
      <div class="breadcrumb"><a href="admin_dashboard.php">Home</a> / Users</div>
    </div>

    <?php if ($message): ?>
    <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px;">
      <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <div style="display:grid; 1fr;gap:20px;align-items:start;">

      <!-- Add User Form -->
      <div class="card-box">
        <h5 style="font-size:15px;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-person-plus" style="color:var(--blue)"></i> Add New User
        </h5>
        <form method="POST">
          <div class="form-group">
            <label class="form-label">Full Name <span>*</span></label>
            <input name="name" type="text" class="form-control" style="max-width:80%;" placeholder="e.g. Maria Santos" required>
          </div>
          <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email <span>*</span></label>
            <input name="email" type="email" class="form-control" placeholder="email@example.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Password <span>*</span></label>
            <input name="password" type="password" class="form-control" placeholder="Set password" required>
          </div>
          </div class="form-row">
          <div class="form-group">
            <label class="form-label">Role <span>*</span></label>
            <select name="role" class="form-select" style="max-width:30%;">
              <option value="user">User (Student)</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button name="add" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Add User
          </button>
        </form>
      </div>

      <!-- Users Table -->
      <div class="table-wrapper">
        <div class="table-header">
          <h5><i class="bi bi-people" style="margin-right:6px;color:var(--blue)"></i>All Users (<?= count($users) ?>)</h5>
          <input type="text" class="form-control" placeholder="Search users…" style="width:200px;padding:8px 12px;font-size:13px;"
                 oninput="filterUsers(this.value)">
        </div>
        <table class="table" id="usersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u['id'] ?></td>
              <td>
                <div style="display:flex;align-items:center;gap:8px;">
                  <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--purple));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;flex-shrink:0;">
                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                  </div>
                  <?= htmlspecialchars($u['name']) ?>
                </div>
              </td>
              <td style="color:var(--text-muted)"><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <?php if ($u['role'] === 'admin'): ?>
                  <span class="badge" style="background:#dbeafe;color:#1e40af;">
                    <i class="bi bi-shield-check" style="font-size:10px;"></i> Admin
                  </span>
                <?php else: ?>
                  <span class="badge" style="background:#f1f5f9;color:#475569;">
                    <i class="bi bi-person" style="font-size:10px;"></i> User
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($u['id'] !== (int)$_SESSION['user']['id']): ?>
                  <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Delete <?= htmlspecialchars($u['name']) ?>?')">
                    <i class="bi bi-trash"></i> Delete
                  </a>
                <?php else: ?>
                  <span style="font-size:12px;color:var(--text-muted);font-style:italic;">You</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<script>
function filterUsers(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#usersTable tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>
</body>
</html>