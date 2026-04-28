<?php
include 'db.php';

if ($_SESSION['user']['role'] !== 'admin') {
  die("Access denied");
}

$result = $conn->query("SELECT * FROM requests");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="style.css">

<?php include 'partials/header.php'; ?>

<div class="d-flex">

  <?php include 'partials/admin_sidebar.php'; ?>

  <div class="main w-100">
    <h3>Requests</h3>

    <table class="table">
      <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Purpose</th>
        <th>Delivery</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php while($row=$result->fetch_assoc()): ?>
      <tr>
        <td><?=$row['id']?></td>
        <td><?=$row['student_name']?></td>
        <td><?=$row['purpose']?></td>
        <td><?=$row['delivery_method']?></td>
        <td><?=$row['status']?></td>
        <td>
          <a href="update_status.php?id=<?=$row['id']?>&status=Approved" class="btn btn-success btn-sm">Approve</a>
          <a href="update_status.php?id=<?=$row['id']?>&status=Rejected" class="btn btn-danger btn-sm">Reject</a>
          <a href="update_status.php?id=<?=$row['id']?>&status=Released" class="btn btn-primary btn-sm">Release</a>
        </td>
      </tr>
      <?php endwhile; ?>

    </table>
  </div>

</div>