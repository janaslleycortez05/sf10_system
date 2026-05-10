<?php
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php"); exit;
}

// Stats
$total      = $conn->query("SELECT COUNT(*) as c FROM requests")->fetch_assoc()['c'];
$pending    = $conn->query("SELECT COUNT(*) as c FROM requests WHERE status='Pending'")->fetch_assoc()['c'];
$rejected   = $conn->query("SELECT COUNT(*) as c FROM requests WHERE status='Rejected'")->fetch_assoc()['c'];
$approved   = $conn->query("SELECT COUNT(*) as c FROM requests WHERE status='Approved'")->fetch_assoc()['c'];
$released   = $conn->query("SELECT COUNT(*) as c FROM requests WHERE status='Released'")->fetch_assoc()['c'];

$result = $conn->query("SELECT r.*, u.email FROM requests r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.id DESC");
$rows   = $result->fetch_all(MYSQLI_ASSOC);

function statusBadge($s) {
  $map = [
    'Pending'    => 'badge-pending',
    'Approved'   => 'badge-approved',
    'Rejected'   => 'badge-rejected',
    'Released'   => 'badge-released'
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
  <title>Admin Dashboard — SF10 Request System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="d-flex">
  <?php include 'partials/admin_sidebar.php'; ?>

  <main class="main" style="display:flex;flex-direction:column;min-height:calc(100vh - var(--navbar-h))">
    <div class="page-header">
      <h2>Admin Dashboard</h2>
      <div class="breadcrumb">Home / Dashboard</div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="grid-template-columns:repeat(5,1fr);width:100%;">
      <div class="stat-card" style="--color:#2563eb;--icon-bg:rgba(37,99,235,.1)">
        <div class="stat-icon"><i class="bi bi-folder2-open"></i></div>
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
        <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="stat-value"><?= $approved ?></div>
        <div class="stat-label">Approved</div>
      </div>
      <div class="stat-card" style="--color:#7c3aed;--icon-bg:rgba(124,58,237,.1)">
        <div class="stat-icon"><i class="bi bi-send-check"></i></div>
        <div class="stat-value"><?= $released ?></div>
        <div class="stat-label">Released</div>
      </div>
    </div>

    <!-- Requests Table -->
    <div class="table-wrapper" style="flex:1;width:100%;">
      <div class="table-header">
        <h5><i class="bi bi-inbox" style="margin-right:6px;color:var(--blue)"></i>All Requests</h5>
        <div style="display:flex;gap:8px;margin-left:auto;">
          <input type="text" id="searchInput" class="form-control" placeholder="Search requests…"
                 style="width:240px;padding:8px 12px;font-size:13px;" oninput="filterTable()">
          <select id="statusFilter" class="form-select" style="width:150px;padding:8px 12px;font-size:13px;" onchange="filterTable()">
            <option value="">All Statuses</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
            <option value="Released">Released</option>
          </select>
        </div>
      </div>

      <?php if (empty($rows)): ?>
      <div style="padding:48px;text-align:center;color:var(--text-muted);">
        <i class="bi bi-inbox" style="font-size:40px;opacity:.35;display:block;margin-bottom:12px;"></i>
        <p>No requests found.</p>
      </div>
      <?php else: ?>
      <table class="table" id="requestTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Student</th>
            <th>LRN</th>
            <th>Purpose</th>
            <th>Delivery</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
          <tr>
            <td><a href="view_request.php?id=<?php echo $row['id']; ?>"><strong>#<?= $row['id'] ?></strong></a></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><code style="font-size:12px;background:#f1f5f9;padding:2px 6px;border-radius:4px;"><?= htmlspecialchars($row['lrn']) ?></code></td>
            <td><?= htmlspecialchars($row['purpose']) ?></td>
            <td>
              <?php if ($row['delivery_method'] === 'Email'): ?>
                <span style="display:flex;align-items:center;gap:4px;"><i class="bi bi-envelope" style="color:var(--blue)"></i> Email</span>
              <?php elseif ($row['delivery_method']): ?>
                <span style="display:flex;align-items:center;gap:4px;"><i class="bi bi-building" style="color:var(--success)"></i> Pickup</span>
              <?php else: ?>
                <span style="color:var(--text-muted)">—</span>
              <?php endif; ?>
            </td>
            <td><?= statusBadge($row['status']) ?></td>
            <td>
              <div style="display:flex;gap:5px;flex-wrap:wrap;">
                <?php if ($row['status'] === 'Pending'): ?>
                  <a href="update_status.php?id=<?= $row['id'] ?>&status=Approved" class="btn btn-success btn-sm">
                    <i class="bi bi-check-lg"></i> Approve
                  </a>
                  <a href="update_status.php?id=<?= $row['id'] ?>&status=Rejected"
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('Reject this request?')">
                    <i class="bi bi-x-lg"></i> Reject
                  </a>
                <?php elseif ($row['status'] === 'Approved'): ?>
                  <a href="generate_document.php?id=<?= $row['id'] ?>" class="btn btn-purple btn-sm">
                    <i class="bi bi-file-earmark-text"></i> Generate
                  </a>
                <?php else: ?>
                  <span style="font-size:12px;color:var(--text-muted);font-style:italic;">No actions</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </main>
</div>

<script>
function filterTable() {
  const q   = document.getElementById('searchInput').value.toLowerCase();
  const st  = document.getElementById('statusFilter').value.toLowerCase();
  document.querySelectorAll('#requestTable tbody tr').forEach(row => {
    const text    = row.textContent.toLowerCase();
    const matchQ  = !q  || text.includes(q);
    const matchSt = !st || text.includes(st);
    row.style.display = (matchQ && matchSt) ? '' : 'none';
  });
}
</script>
</body>
</html>