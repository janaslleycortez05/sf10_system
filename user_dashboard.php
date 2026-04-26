<?php include 'db.php';
if(!isset($_SESSION['user'])) header("Location: login.php");

$user_id=$_SESSION['user']['id'];
$result = $conn->query("SELECT * FROM requests WHERE user_id=$user_id");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<?php include 'partials/header.php'; ?>

<div class="d-flex">
<?php include 'partials/sidebar.php'; ?>

<div class="main w-100">
  <h3>My Requests</h3>
  <table class="table">
    <tr><th>Request ID</th><th>Purpose</th><th>Status</th></tr>

    <?php while($row=$result->fetch_assoc()): ?>
    <tr>
      <td>#<?=$row['id']?></td>
      <td><?=$row['purpose']?></td>
      <td><?=$row['status']?></td>
    </tr>
    <?php endwhile; ?>

  </table>
</div>
</div>