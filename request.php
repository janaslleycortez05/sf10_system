<?php include 'db.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $uid=$_SESSION['user']['id'];
  $name=$_POST['name'];
  $lrn=$_POST['lrn'];
  $purpose=$_POST['purpose'];
  $delivery = $_POST['delivery'];
  $email_delivery = $_POST['email_delivery'] ?? '';

  $conn->query("INSERT INTO requests(user_id, student_name, lrn, purpose, delivery_method)
  VALUES('$uid','$name','$lrn','$purpose','$delivery')");
  header("Location: user_dashboard.php");
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<?php include 'partials/header.php'; ?>
<div class="d-flex">
<?php include 'partials/sidebar.php'; ?>
<div class="main w-100">
<h3>New Request</h3>

<form method="POST">
  <input name="name" class="form-control my-2" placeholder="Student Name">
  <input name="lrn" class="form-control my-2" placeholder="LRN">

  <select name="purpose" class="form-select my-2">
    <option>Transfer to another school</option>
    <option>Employment</option>
    <option>Others</option>
  </select>

  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="delivery" value="Pickup" required>
    <label class="form-check-label">Pickup</label>
  </div>

  <div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="delivery" value="Email">
    <label class="form-check-label">Email</label>
  </div>
  <input type="email" name="email_delivery" class="form-control my-2" placeholder="Enter email (if Email selected)">
  
  <button class="btn btn-primary">Submit</button>
</form>
</div>