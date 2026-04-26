<?php include 'db.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $email=$_POST['email'];
  $pass=$_POST['password'];

  $res=$conn->query("SELECT * FROM users WHERE email='$email' AND password='$pass'");
  if($res->num_rows>0){
    $_SESSION['user']=$res->fetch_assoc();

    if($_SESSION['user']['role']=="admin"){
      header("Location: admin_dashboard.php");
    } else {
      header("Location: user_dashboard.php");
    }
  } else {
    $error="Invalid login";
  }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
  <form method="POST" class="card p-4 shadow" style="width:350px;">
    <h4 class="text-center">Login</h4>
    <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <input name="email" class="form-control my-2" placeholder="Email">
    <input name="password" type="password" class="form-control my-2" placeholder="Password">
    <button class="btn btn-primary w-100">Login</button>
    <p class="text-center mt-3">
      Don’t have an account yet?
      <a href="register.php">Sign up here</a>
    </p>
  </form>
</div>