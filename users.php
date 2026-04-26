<?php
include 'db.php';

// Only allow admin
if ($_SESSION['user']['role'] != 'admin') {
  header("Location: login.php");
}

// ADD USER
if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  $conn->query("INSERT INTO users(name,email,password,role)
  VALUES('$name','$email','$password','$role')");
}

// DELETE USER
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM users WHERE id=$id");
}

// FETCH USERS
$result = $conn->query("SELECT * FROM users");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<?php include 'partials/header.php'; ?>

<div class="d-flex">
<?php include 'partials/admin_sidebar.php'; ?>

<div class="main w-100">

  <h3>Users Management</h3>

  <!-- ADD USER FORM -->
  <div class="card-box mb-4">
    <h5>Add User</h5>
    <form method="POST">
      <input name="name" class="form-control my-2" placeholder="Full Name" required>
      <input name="email" class="form-control my-2" placeholder="Email" required>
      <input name="password" type="password" class="form-control my-2" placeholder="Password" required>

      <select name="role" class="form-control my-2">
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>

      <button name="add" class="btn btn-primary">Add User</button>
    </form>
  </div>

  <!-- USERS TABLE -->
  <div class="card-box">
    <h5>All Users</h5>

    <table class="table table-bordered">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Action</th>
      </tr>

      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?=$row['id']?></td>
        <td><?=$row['name']?></td>
        <td><?=$row['email']?></td>
        <td><?=$row['role']?></td>
        <td>
          <a href="?delete=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>

    </table>
  </div>

</div>
</div>