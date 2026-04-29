<?php
include 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user']['id'];
$result  = $conn->query("SELECT * FROM requests WHERE user_id = $user_id ORDER BY id DESC");
$rows    = $result->fetch_all(MYSQLI_ASSOC);

// Counts
$total    = count($rows);
$pending  = count(array_filter($rows, fn($r) => $r['status'] === 'Pending'));
$rejected   = count(array_filter($rows, fn($r) => $r['status'] === 'Rejected'));
$approved = count(array_filter($rows, fn($r) => $r['status'] === 'Approved'));
$released = count(array_filter($rows, fn($r) => $r['status'] === 'Released'));

function statusBadge($s) {
  $map = [
    'Pending'    => 'badge-pending',
    'Approved'   => 'badge-approved',
    'Rejected'   => 'badge-rejected',
    'Released'   => 'badge-released',
    'Processing' => 'badge-processing',
  ];
  $cls = $map[$s] ?? 'badge-pending';
  return "<span class='badge $cls'>$s</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/sidebar.php'; ?>

  <main class="main">
    <div class="page-header">
      <h2>Dashboard</h2>
      <div class="breadcrumb">Home / Dashboard</div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card" style="--color:#2563eb;--icon-bg:rgba(37,99,235,.1)">
        <div class="stat-icon"><i class="bi bi-folder2"></i></div>
        <div class="stat-value"><?= $total ?></div>
        <div class="stat-label">Total Requests</div>
      </div>
      <div class="stat-card" style="--color:#f59e0b;--icon-bg:rgba(245,158,11,.1)">
        <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-value"><?= $pending ?></div>
        <div class="stat-label">Pending</div>
      </div>
      <div class="stat-card" style="--color:#dc143c;--icon-bg:rgba(158, 45, 60, 0.1)">
        <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
        <div class="stat-value"><?= $rejected ?></div>
        <div class="stat-label">Rejected</div>
      </div>
      <div class="stat-card" style="--color:#10b981;--icon-bg:rgba(16,185,129,.1)">
        <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
        <div class="stat-value"><?= $approved ?></div>
        <div class="stat-label">Approved</div>
      </div>
      <div class="stat-card" style="--color:#7c3aed;--icon-bg:rgba(124,58,237,.1)">
        <div class="stat-icon"><i class="bi bi-send-check"></i></div>
        <div class="stat-value"><?= $released ?></div>
        <div class="stat-label">Released</div>
      </div>
    </div>

    <!-- Quick Action -->
    <div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
      <a href="request.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New SF10 Request
      </a>
    </div>

    <!-- Requests Table -->
    <div class="table-wrapper">
      <div class="table-header">
        <h5><i class="bi bi-card-list" style="margin-right:6px;color:var(--blue)"></i>My Requests</h5>
      </div>
      <?php if (empty($rows)): ?>
      <div style="padding:48px;text-align:center;color:var(--text-muted);">
        <i class="bi bi-inbox" style="font-size:40px;opacity:.35;display:block;margin-bottom:12px;"></i>
        <p style="font-size:14px;">No requests yet. <a href="request.php" style="color:var(--blue);font-weight:600;">Submit your first request</a></p>
      </div>
      <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th>Request ID</th>
            <th>Student Name</th>
            <th>Purpose</th>
            <th>Delivery</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
          <tr>
            <td><strong>#<?= $row['id'] ?></strong></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['purpose']) ?></td>
            <td>
              <?php if ($row['delivery_method'] === 'Email'): ?>
                <span style="display:flex;align-items:center;gap:5px;">
                  <i class="bi bi-envelope" style="color:var(--blue)"></i> Email
                </span>
              <?php else: ?>
                <span style="display:flex;align-items:center;gap:5px;">
                  <i class="bi bi-building" style="color:var(--success)"></i> Pickup
                </span>
              <?php endif; ?>
            </td>
            <td><?= statusBadge($row['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>