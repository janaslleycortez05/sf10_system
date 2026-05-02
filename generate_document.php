<?php
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php"); exit;
}

$request_id = (int)($_GET['id'] ?? 0);
if (!$request_id) { header("Location: admin_dashboard.php"); exit; }

// Fetch request
$req = $conn->query("SELECT r.*, u.email as user_email FROM requests r LEFT JOIN users u ON r.user_id = u.id WHERE r.id = $request_id")->fetch_assoc();
if (!$req) { header("Location: admin_dashboard.php"); exit; }

// Find matching SF10 record by LRN AND student name
$lrn  = $conn->real_escape_string(trim($req['lrn']));
$name = $conn->real_escape_string(trim($req['student_name']));
$record = $conn->query("SELECT * FROM sf10_records WHERE lrn = '$lrn' AND student_name = '$name' LIMIT 1")->fetch_assoc();

// If no exact match, try LRN only (looser match, shown as warning)
$loose_match = null;
if (!$record) {
  $loose_match = $conn->query("SELECT * FROM sf10_records WHERE lrn = '$lrn' LIMIT 1")->fetch_assoc();
}

$delivery = $req['delivery_method'];
$email_to = $req['email_delivery'] ?: $req['user_email'];

// Handle Release action (after confirmation on this page)
if (isset($_POST['release'])) {
  $conn->query("UPDATE requests SET status='Released' WHERE id = $request_id");
  header("Location: admin_dashboard.php?released=1");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Generation — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .step-row { display:flex; align-items:flex-start; gap:14px; margin-bottom:20px; }
    .step-dot {
      width:32px; height:32px; border-radius:50%; display:flex; align-items:center;
      justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; margin-top:2px;
    }
    .step-dot.done  { background:var(--success); color:white; }
    .step-dot.fail  { background:var(--danger);  color:white; }
    .step-dot.wait  { background:var(--border);  color:var(--text-muted); }
    .step-content { flex:1; }
    .step-title { font-size:14px; font-weight:700; color:var(--text); }
    .step-desc  { font-size:12.5px; color:var(--text-muted); margin-top:3px; }
    .info-grid  { display:grid; grid-template-columns:1fr 1fr; gap:10px 24px; margin-top:12px; }
    .info-item label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); display:block; }
    .info-item span  { font-size:13.5px; font-weight:600; color:var(--text); }
    @media print {
      .no-print { display:none !important; }
      .main { margin-left:0 !important; }
      nav, aside { display:none !important; }
      body { padding-top:0 !important; }
    }
  </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/admin_sidebar.php'; ?>

  <main class="main">
    <div class="page-header no-print" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div>
        <h2>Document Generation</h2>
        <div class="breadcrumb"><a href="admin_dashboard.php">Home</a> / <a href="admin_dashboard.php">Requests</a> / Request #<?= $request_id ?></div>
      </div>
      <a href="admin_dashboard.php" class="btn btn-outline no-print">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

      <div>

        <!-- Request Summary Card -->
        <div class="card-box mb-4">
          <h5 style="font-size:14px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-person-vcard" style="color:var(--blue)"></i> Request Information
          </h5>
          <div class="info-grid">
            <div class="info-item"><label>Student Name</label><span><?= htmlspecialchars($req['student_name']) ?></span></div>
            <div class="info-item"><label>LRN</label><span><?= htmlspecialchars($req['lrn']) ?></span></div>
            <div class="info-item"><label>Purpose</label><span><?= htmlspecialchars($req['purpose']) ?></span></div>
            <div class="info-item"><label>Delivery Method</label>
              <span>
                <?php if ($delivery === 'Email'): ?>
                  <i class="bi bi-envelope" style="color:var(--blue)"></i> Email — <?= htmlspecialchars($email_to) ?>
                <?php else: ?>
                  <i class="bi bi-building" style="color:var(--success)"></i> Pickup
                <?php endif; ?>
              </span>
            </div>
          </div>
        </div>

        <?php if ($record): ?>
        <!-- ── RECORD FOUND ── -->

        <!-- SF10 Record Card -->
        <div class="card-box mb-4">
          <h5 style="font-size:14px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-file-earmark-check" style="color:var(--success)"></i> SF10 Record Found
          </h5>
          <div class="info-grid">
            <div class="info-item"><label>Student Name</label><span><?= htmlspecialchars($record['student_name']) ?></span></div>
            <div class="info-item"><label>LRN</label><span><?= htmlspecialchars($record['lrn']) ?></span></div>
            <div class="info-item"><label>Date of Birth</label><span><?= $record['date_of_birth'] ? date('M d, Y', strtotime($record['date_of_birth'])) : '—' ?></span></div>
            <div class="info-item"><label>Grade Level</label><span><?= htmlspecialchars($record['grade_level'] ?? '—') ?></span></div>
            <div class="info-item"><label>School Year</label><span><?= htmlspecialchars($record['school_year'] ?? '—') ?></span></div>
            <div class="info-item"><label>Section</label><span><?= htmlspecialchars($record['section'] ?? '—') ?></span></div>
            <?php if ($record['notes']): ?>
            <div class="info-item" style="grid-column:1/-1;"><label>Notes</label><span><?= htmlspecialchars($record['notes']) ?></span></div>
            <?php endif; ?>
          </div>

          <?php if ($record['sf10_file']): ?>
          <div style="margin-top:16px;padding:14px;background:#f8fafc;border-radius:var(--radius-sm);border:1px solid var(--border);display:flex;align-items:center;gap:12px;">
            <i class="bi bi-file-earmark-pdf" style="font-size:28px;color:var(--danger);"></i>
            <div style="flex:1;">
              <div style="font-size:13px;font-weight:600;"><?= htmlspecialchars($record['sf10_file']) ?></div>
              <div style="font-size:11.5px;color:var(--text-muted);">Uploaded SF10 document</div>
            </div>
            <a href="uploads/records/<?= urlencode($record['sf10_file']) ?>" target="_blank" class="btn btn-outline btn-sm no-print">
              <i class="bi bi-eye"></i> Preview
            </a>
          </div>
          <?php endif; ?>
        </div>

        <!-- Action Card -->
        <div class="card-box no-print">
          <h5 style="font-size:14px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-send-check" style="color:var(--purple)"></i> Release Document
          </h5>

          <?php if ($delivery === 'Pickup'): ?>
          <!-- PICKUP: Print action -->
          <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
            This request is set for <strong>pickup</strong>. Print the SF10 document and mark it as released once the student claims it.
          </p>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <?php if ($record['sf10_file']): ?>
            <a href="uploads/records/<?= urlencode($record['sf10_file']) ?>" target="_blank" class="btn btn-outline">
              <i class="bi bi-printer"></i> Open & Print Document
            </a>
            <?php else: ?>
            <button class="btn btn-outline" onclick="window.print()">
              <i class="bi bi-printer"></i> Print This Page
            </button>
            <?php endif; ?>
            <form method="POST" style="display:inline;">
              <button name="release" type="submit" class="btn btn-success"
                      onclick="return confirm('Mark this request as Released?')">
                <i class="bi bi-check2-circle"></i> Mark as Released
              </button>
            </form>
          </div>

          <?php else: ?>
          <!-- EMAIL: Gmail compose action -->
          <?php
            $subject = urlencode("Your SF10 Document — " . $req['student_name']);
            $body    = urlencode(
              "Dear " . $req['student_name'] . ",\n\n" .
              "Your SF10 document request has been approved and is now ready.\n\n" .
              "Request Details:\n" .
              "- LRN: " . $req['lrn'] . "\n" .
              "- Purpose: " . $req['purpose'] . "\n\n" .
              "Please find your attached SF10 document.\n\n" .
              "Lazaan-Malinao Elementary School"
            );
            $gmail_url = "https://mail.google.com/mail/?view=cm&to=" . urlencode($email_to) . "&su=$subject&body=$body";
          ?>
          <p style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">
            This request will be sent via <strong>email</strong> to:
          </p>
          <div style="font-size:14px;font-weight:600;color:var(--blue);margin-bottom:16px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-envelope-fill"></i> <?= htmlspecialchars($email_to) ?>
          </div>
          <p style="font-size:12.5px;color:var(--text-muted);margin-bottom:16px;">
            Clicking the button below will open Gmail with a pre-filled message. Attach the SF10 file manually before sending.
          </p>
          <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?= $gmail_url ?>" target="_blank" class="btn btn-primary">
              <i class="bi bi-envelope-arrow-up"></i> Open Gmail to Send
            </a>
            <form method="POST" style="display:inline;">
              <button name="release" type="submit" class="btn btn-success"
                      onclick="return confirm('Mark this request as Released after sending the email?')">
                <i class="bi bi-check2-circle"></i> Mark as Released
              </button>
            </form>
          </div>
          <?php endif; ?>
        </div>

        <?php elseif ($loose_match): ?>
        <!-- ── LOOSE MATCH (LRN found but name differs) ── -->
        <div class="card-box" style="border:2px solid #fcd34d;">
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi bi-exclamation-triangle-fill" style="color:#d97706;font-size:20px;"></i>
            </div>
            <div>
              <div style="font-size:15px;font-weight:700;color:#92400e;margin-bottom:6px;">Name Mismatch</div>
              <p style="font-size:13px;color:#78350f;margin-bottom:12px;">
                A record with LRN <strong><?= htmlspecialchars($req['lrn']) ?></strong> was found, but the name doesn't match.<br>
                Request name: <strong><?= htmlspecialchars($req['student_name']) ?></strong> &nbsp;|&nbsp;
                Record name: <strong><?= htmlspecialchars($loose_match['student_name']) ?></strong>
              </p>
              <p style="font-size:13px;color:#78350f;margin-bottom:16px;">
                Please verify the correct student and update either the request or the record before releasing.
              </p>
              <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="records.php" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i> Go to Records to Fix
                </a>
              </div>
            </div>
          </div>
        </div>

        <?php else: ?>
        <!-- ── NO RECORD FOUND ── -->
        <div class="card-box" style="border:2px solid #fecaca;">
          <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi bi-x-circle-fill" style="color:var(--danger);font-size:20px;"></i>
            </div>
            <div>
              <div style="font-size:15px;font-weight:700;color:#991b1b;margin-bottom:6px;">Document Not Ready</div>
              <p style="font-size:13px;color:#7f1d1d;margin-bottom:12px;">
                No SF10 record was found for:<br>
                <strong>Name:</strong> <?= htmlspecialchars($req['student_name']) ?> &nbsp;|&nbsp;
                <strong>LRN:</strong> <?= htmlspecialchars($req['lrn']) ?>
              </p>
              <p style="font-size:13px;color:#7f1d1d;margin-bottom:16px;">
                Please do one of the following before releasing:
              </p>
              <ul style="font-size:13px;color:#7f1d1d;margin-bottom:16px;padding-left:18px;line-height:1.9;">
                <li>Upload the SF10 file in the <strong>Records</strong> section with the exact same name and LRN.</li>
                <li>Check if the student's name or LRN in the request is spelled correctly.</li>
                <li>Verify the record exists and is not filed under a different name or LRN.</li>
              </ul>
              <a href="records.php" class="btn btn-primary btn-sm">
                <i class="bi bi-archive"></i> Go to Records
              </a>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>

      <!-- Status Steps -->
      <div class="card-box no-print" style="position:sticky;top:calc(var(--navbar-h) + 20px);">
        <h5 style="font-size:14px;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
          <i class="bi bi-list-check" style="color:var(--blue)"></i> Processing Steps
        </h5>

        <div class="step-row">
          <div class="step-dot done"><i class="bi bi-check-lg"></i></div>
          <div class="step-content">
            <div class="step-title">Request Submitted</div>
            <div class="step-desc">Student submitted the SF10 request.</div>
          </div>
        </div>

        <div class="step-row">
          <div class="step-dot done"><i class="bi bi-check-lg"></i></div>
          <div class="step-content">
            <div class="step-title">Request Approved</div>
            <div class="step-desc">Admin approved the request.</div>
          </div>
        </div>

        <div class="step-row">
          <div class="step-dot <?= $record ? 'done' : 'fail' ?>">
            <i class="bi bi-<?= $record ? 'check-lg' : 'x-lg' ?>"></i>
          </div>
          <div class="step-content">
            <div class="step-title">SF10 Record Located</div>
            <div class="step-desc"><?= $record ? 'Matching record found in the database.' : 'No matching record found.' ?></div>
          </div>
        </div>

        <div class="step-row">
          <div class="step-dot <?= $record ? ($delivery === 'Email' ? 'wait' : 'wait') : 'wait' ?>">4</div>
          <div class="step-content">
            <div class="step-title"><?= $delivery === 'Email' ? 'Send via Email' : 'Print & Pickup' ?></div>
            <div class="step-desc"><?= $delivery === 'Email' ? 'Send document to ' . htmlspecialchars($email_to) : 'Print and hand to student.' ?></div>
          </div>
        </div>

        <div class="step-row" style="margin-bottom:0;">
          <div class="step-dot wait">5</div>
          <div class="step-content">
            <div class="step-title">Mark as Released</div>
            <div class="step-desc">Request status will be set to Released.</div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

</body>
</html>