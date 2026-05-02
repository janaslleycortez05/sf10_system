<?php
include 'db.php';
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $uid        = $_SESSION['user']['id'];
  $name       = $conn->real_escape_string(trim($_POST['name']));
  $dob        = $conn->real_escape_string($_POST['dob']);
  $lrn        = $conn->real_escape_string(trim($_POST['lrn']));
  $school_yr  = $conn->real_escape_string($_POST['last-school-year']);
  $purpose    = $conn->real_escape_string($_POST['purpose']);
  $delivery   = $conn->real_escape_string($_POST['delivery']);
  $email_del  = $conn->real_escape_string(trim($_POST['email_delivery'] ?? ''));

  // Handle file upload
  $uploadedFile = null;
  if (!empty($_FILES['valid_id']['name'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext      = pathinfo($_FILES['valid_id']['name'], PATHINFO_EXTENSION);
    $filename = 'id_' . $uid . '_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['valid_id']['tmp_name'], $uploadDir . $filename);
    $uploadedFile = $conn->real_escape_string($filename);
  }

  $conn->query("INSERT INTO requests (user_id, student_name, lrn, date_of_birth, school_year, purpose, delivery_method, email_delivery, valid_id)
    VALUES ('$uid', '$name', '$lrn', '$dob', '$school_yr', '$purpose', '$delivery', '$email_del', " . ($uploadedFile ? "'$uploadedFile'" : "NULL") . ")");
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

      <div>
        <div class="card-box">

          <!-- Section 1: Learner Info -->
          <div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
            <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
              <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</span>
              Learner Information
            </h5>

            <form method="POST" id="requestForm" enctype="multipart/form-data">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px; max-width:80%;">
                <div class="form-group">
                  <label class="form-label">Student Name <span>*</span></label>
                  <input name="name" type="text" class="form-control" placeholder="e.g. Juan Dela Cruz"
                    value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" required>
                </div>
                <div class="form-group"  style="max-width: 30%;">
                  <label class="form-label">Date of Birth <span>*</span></label>
                  <input name="dob" type="date" class="form-control" required>
                </div>
                <div class="form-group">
                  <label class="form-label">LRN (Learner Reference No.) <span>*</span></label>
                  <input name="lrn" type="text" class="form-control" placeholder="e.g. 123456789012" required>
                </div>
                <div class="form-group"  style="max-width: 30%;">
                  <label class="form-label">Last School Year Attended <span>*</span></label>
                  <select name="last-school-year" class="form-select" required>
                    <?php
                    $currentYear = date("Y");
                    for ($i = $currentYear; $i >= 1920; $i--) {
                      $selected = ($i == $currentYear) ? 'selected' : '';
                      echo "<option value='{$i}' {$selected}>{$i}</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
          </div>

          <!-- Section 2: Request Details -->
          <div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
            <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
              <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</span>
              Request Details
            </h5>

            <div class="form-group" style="max-width:50%;">
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

          <!-- Section 3: Upload Valid ID -->
          <div style="border-bottom:1px solid var(--border);padding-bottom:20px;margin-bottom:20px;">
            <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
              <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">3</span>
              Upload Valid ID
            </h5>

            <div class="form-group" style="max-width:50%">
              <label class="form-label">Valid ID <span>*</span></label>
              <label id="uploadArea" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed var(--border);border-radius:var(--radius);padding:32px 20px;cursor:pointer;transition:var(--transition);background:#fafbfc;">
                <i class="bi bi-cloud-arrow-up" style="font-size:32px;color:var(--blue);"></i>
                <div style="font-weight:600;font-size:13px;color:var(--text);">Click to upload or drag and drop</div>
                <div style="font-size:11.5px;color:var(--text-muted);">JPG, PNG, PDF — max 5MB</div>
                <input type="file" name="valid_id" id="validIdInput" accept=".jpg,.jpeg,.png,.pdf"
                  style="display:none;" onchange="previewFile(this)">
              </label>
              <div id="filePreview" style="display:none;margin-top:12px;padding:10px 14px;background:#f1f5f9;border-radius:var(--radius-sm);align-items:center;gap:10px;">
                <i class="bi bi-file-earmark-check" style="font-size:20px;color:var(--success);"></i>
                <div style="flex:1;min-width:0;">
                  <div id="fileName" style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>
                  <div id="fileSize" style="font-size:11px;color:var(--text-muted);"></div>
                </div>
                <button type="button" onclick="clearFile()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:16px;padding:0;">
                  <i class="bi bi-x-circle"></i>
                </button>
              </div>
              <p class="form-hint" style="margin-top:8px;">Accepted: School ID, Birth Certificate, or any government-issued ID.</p>
            </div>
          </div>

          <!-- Section 4: Delivery Method -->
          <div style="margin-bottom:24px;">
            <h5 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
              <span style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">4</span>
              Delivery Method
            </h5>

            <div class="radio-group" id="deliveryGroup" style="max-width:50%;">
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

            <div id="emailField" style="display:none;margin-top:14px;max-width:50%;">
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
    document.querySelectorAll('input[name="delivery"]').forEach(r => {
      r.addEventListener('change', () => {
        document.querySelectorAll('.radio-option').forEach(o => o.classList.remove('selected'));
        r.closest('.radio-option').classList.add('selected');
      });
    });

    function previewFile(input) {
      const file = input.files[0];
      if (!file) return;
      document.getElementById('fileName').textContent = file.name;
      document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
      document.getElementById('filePreview').style.display = 'flex';
      document.getElementById('uploadArea').style.borderColor = 'var(--success)';
      document.getElementById('uploadArea').style.background = 'rgba(16,185,129,.04)';
    }

    function clearFile() {
      document.getElementById('validIdInput').value = '';
      document.getElementById('filePreview').style.display = 'none';
      document.getElementById('uploadArea').style.borderColor = 'var(--border)';
      document.getElementById('uploadArea').style.background = '#fafbfc';
    }

    const area = document.getElementById('uploadArea');
    area.addEventListener('dragover', e => {
      e.preventDefault();
      area.style.borderColor = 'var(--blue)';
      area.style.background = 'rgba(37,99,235,.04)';
    });
    area.addEventListener('dragleave', () => {
      area.style.borderColor = 'var(--border)';
      area.style.background = '#fafbfc';
    });
    area.addEventListener('drop', e => {
      e.preventDefault();
      const dt = e.dataTransfer.files;
      if (dt[0]) {
        document.getElementById('validIdInput').files = dt;
        previewFile(document.getElementById('validIdInput'));
      }
    });
  </script>
</body>

</html>