<?php
include 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $uid      = $_SESSION['user']['id'];
  $name     = $conn->real_escape_string(trim($_POST['name']));
  $lrn      = $conn->real_escape_string(trim($_POST['lrn']));
  $purpose  = $conn->real_escape_string($_POST['purpose']);
  $delivery = $conn->real_escape_string($_POST['delivery']);

  $conn->query("INSERT INTO requests (user_id, student_name, lrn, purpose, delivery_method)
    VALUES ('$uid', '$name', '$lrn', '$purpose', '$delivery')");
  header("Location: user_dashboard.php?submitted=1");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Request — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

  <main class="main">
    <div class="page-header">
      <h2>New SF10 Request</h2>
      <div class="breadcrumb"><a href="user_dashboard.php">Home</a> / New Request</div>
    </div>

    <div style="max-width100%;">
      <div class="card-box">

        <!-- Section: Learner Info -->
        <div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
          <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</span>
            Learner Information
          </h5>

          <form method="POST" id="requestForm">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Student Name <span>*</span></label>
                <input name="name" type="text" class="form-control" placeholder="e.g. Juan Dela Cruz"
                       value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">LRN (Learner Reference No.) <span>*</span></label>
                <input name="lrn" type="text" class="form-control" placeholder="e.g. 123456789012" required>
              </div>
            </div>
        </div>

        <!-- Section: Request Details -->
        <div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
          <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</span>
            Request Details
          </h5>

          <div class="form-group" style="width:50%;">
            <label class="form-label">Purpose <span>*</span></label>
            <select name="purpose" class="form-select" required>
              <option value="" disabled selected>Select purpose of request</option>
              <option value="Transfer to Another School">Transfer to Another School</option>
              <option value="Employment">Employment</option>
              <option value="Scholarship">Scholarship</option>
              <option value="Personal">Personal</option>
              <option value="Others">Others</option>
            </select>
          </div>
        </div>

        <!-- Section: Delivery Method -->
        <div style="margin-bottom:24px;">
          <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">3</span>
            Delivery Method
          </h5>

          <div class="radio-group" id="deliveryGroup">
            <label class="radio-option" id="opt-pickup">
              <input type="radio" name="delivery" value="Pickup" required onclick="toggleEmail(false)">
              <i class="bi bi-building" style="font-size:18px;color:var(--success)"></i>
              <div>
                <div style="font-weight:600;">Pickup</div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:400;">Claim at the school</div>
              </div>
            </label>
            <label class="radio-option" id="opt-email">
              <input type="radio" name="delivery" value="Email" onclick="toggleEmail(true)">
              <i class="bi bi-envelope" style="font-size:18px;color:var(--blue)"></i>
              <div>
                <div style="font-weight:600;">Email Delivery</div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:400;">Receive via email</div>
              </div>
            </label>
          </div>

          <div id="emailField" style="display:none;margin-top:14px;width:50%;">
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <div class="input-icon-wrap">
                <i class="bi bi-envelope"></i>
                <input name="email_delivery" type="email" class="form-control"
                       placeholder="Enter email for delivery"
                       value="<?= htmlspecialchars($_SESSION['user']['email']) ?>">
              </div>
              <p class="form-hint">Your SF10 document will be sent to this email once released.</p>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div style="display:flex;gap:10px;justify-content:flex-end;">
          <a href="user_dashboard.php" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-send"></i> Submit Request
          </button>
        </div>
          </form>

      </div>
    </div>
  </main>
</div>

<script>
function toggleEmail(show) {
  document.getElementById('emailField').style.display = show ? 'block' : 'none';
  document.querySelector('#opt-pickup').classList.toggle('selected', !show);
  document.querySelector('#opt-email').classList.toggle('selected', show);
}
// Highlight on load
document.querySelectorAll('input[name="delivery"]').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('.radio-option').forEach(o => o.classList.remove('selected'));
    r.closest('.radio-option').classList.add('selected');
  });
});
</script>
</body>
</html>