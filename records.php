<?php
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
	header("Location: login.php");
	exit;
}

$message = '';
$message_type = 'success';

// Delete record
if (isset($_GET['delete'])) {
	$id = (int)$_GET['delete'];
	// Delete associated file if exists
	$row = $conn->query("SELECT sf10_file FROM sf10_records WHERE id = $id")->fetch_assoc();
	if ($row && $row['sf10_file'] && file_exists('uploads/records/' . $row['sf10_file'])) {
		unlink('uploads/records/' . $row['sf10_file']);
	}
	$conn->query("DELETE FROM sf10_records WHERE id = $id");
	$message = "Record deleted successfully.";
}

// Add record
if (isset($_POST['add'])) {
	$student_name = $conn->real_escape_string(trim($_POST['student_name']));
	$lrn          = $conn->real_escape_string(trim($_POST['lrn']));
	$dob          = $conn->real_escape_string($_POST['dob']);
	$grade        = $conn->real_escape_string($_POST['grade_level']);
	$school_year  = $conn->real_escape_string(trim($_POST['school_year']));
	$section      = $conn->real_escape_string(trim($_POST['section']));
	$notes        = $conn->real_escape_string(trim($_POST['notes']));

	$uploadedFile = null;
	if (!empty($_FILES['sf10_file']['name'])) {
		$uploadDir = 'uploads/records/';
		if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
		$ext      = pathinfo($_FILES['sf10_file']['name'], PATHINFO_EXTENSION);
		$filename = 'sf10_' . preg_replace('/\s+/', '_', $student_name) . '_' . time() . '.' . $ext;
		move_uploaded_file($_FILES['sf10_file']['tmp_name'], $uploadDir . $filename);
		$uploadedFile = $conn->real_escape_string($filename);
	}

	$conn->query("INSERT INTO sf10_records (student_name, lrn, date_of_birth, grade_level, school_year, section, notes, sf10_file)
    VALUES ('$student_name','$lrn','$dob','$grade','$school_year','$section','$notes'," . ($uploadedFile ? "'$uploadedFile'" : "NULL") . ")");
	$message = "Record added successfully.";
}

// Modify record
$editRow = null;
if (isset($_GET['edit'])) {
	$id = (int)$_GET['edit'];
	$editRow = $conn->query("SELECT * FROM sf10_records WHERE id = $id")->fetch_assoc();
}

// Update record
if (isset($_POST['update'])) {
	$id           = (int)$_POST['edit_id'];
	$student_name = $conn->real_escape_string(trim($_POST['student_name']));
	$lrn          = $conn->real_escape_string(trim($_POST['lrn']));
	$dob          = $conn->real_escape_string($_POST['dob']);
	$grade        = $conn->real_escape_string($_POST['grade_level']);
	$school_year  = $conn->real_escape_string(trim($_POST['school_year']));
	$section      = $conn->real_escape_string(trim($_POST['section']));
	$notes        = $conn->real_escape_string(trim($_POST['notes']));

	// Handle new file upload on edit
	$fileClause = '';
	if (!empty($_FILES['sf10_file']['name'])) {
		$uploadDir = 'uploads/records/';
		if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
		// Delete old file
		$old = $conn->query("SELECT sf10_file FROM sf10_records WHERE id = $id")->fetch_assoc();
		if ($old && $old['sf10_file'] && file_exists($uploadDir . $old['sf10_file'])) {
			unlink($uploadDir . $old['sf10_file']);
		}
		$ext      = pathinfo($_FILES['sf10_file']['name'], PATHINFO_EXTENSION);
		$filename = 'sf10_' . preg_replace('/\s+/', '_', $student_name) . '_' . time() . '.' . $ext;
		move_uploaded_file($_FILES['sf10_file']['tmp_name'], $uploadDir . $filename);
		$fileClause = ", sf10_file = '" . $conn->real_escape_string($filename) . "'";
	}

	$conn->query("UPDATE sf10_records SET
    student_name='$student_name', lrn='$lrn', date_of_birth='$dob',
    grade_level='$grade', school_year='$school_year', section='$section', notes='$notes'
    $fileClause WHERE id = $id");
	$message = "Record updated successfully.";
}

// Search via student namre ot LRN
$search = $conn->real_escape_string(trim($_GET['q'] ?? ''));
$where  = $search ? "WHERE student_name LIKE '%$search%' OR lrn LIKE '%$search%'" : '';
$records = $conn->query("SELECT * FROM sf10_records $where ORDER BY student_name ASC")->fetch_all(MYSQLI_ASSOC);
$total   = count($records);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SF10 Records — Admin</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
</head>

<body>

	<?php include 'partials/header.php'; ?>

	<div class="d-flex">
		<?php include 'partials/admin_sidebar.php'; ?>

		<main class="main">
			<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
				<div>
					<h2>SF10 Records</h2>
					<div class="breadcrumb"><a href="admin_dashboard.php">Home</a> / Records</div>
				</div>
				<button class="btn btn-primary" onclick="openModal('addModal')">
					<i class="bi bi-plus-lg"></i> Add Record
				</button>
			</div>

			<?php if ($message): ?>
				<div style="background:<?= $message_type === 'error' ? '#fee2e2' : '#d1fae5' ?>;border:1px solid <?= $message_type === 'error' ? '#fecaca' : '#6ee7b7' ?>;color:<?= $message_type === 'error' ? '#991b1b' : '#065f46' ?>;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px;">
					<i class="bi bi-<?= $message_type === 'error' ? 'exclamation-circle' : 'check-circle' ?>-fill"></i>
					<?= htmlspecialchars($message) ?>
				</div>
			<?php endif; ?>

			<!-- Records Table -->
			<div class="table-wrapper" style="width:100%;">
				<div class="table-header" style="flex-wrap:wrap;gap:12px;">
					<h5><i class="bi bi-archive" style="margin-right:6px;color:var(--blue)"></i>All SF10 Records <span style="font-weight:400;color:var(--text-muted);font-size:13px;">(<?= $total ?>)</span></h5>
					<form method="GET" style="display:flex;gap:8px;margin-left:auto;">
						<div class="input-icon-wrap">
							<i class="bi bi-search"></i>
							<input type="text" name="q" class="form-control" placeholder="Search by name or LRN…"
								value="<?= htmlspecialchars($search) ?>"
								style="width:280px;padding:8px 12px 8px 36px;font-size:13px;">
						</div>
						<button type="submit" class="btn btn-primary btn-sm">Search</button>
						<?php if ($search): ?>
							<a href="records.php" class="btn btn-outline btn-sm">Clear</a>
						<?php endif; ?>
					</form>
				</div>

				<?php if (empty($records)): ?>
					<div style="padding:56px;text-align:center;color:var(--text-muted);">
						<i class="bi bi-archive" style="font-size:44px;opacity:.25;display:block;margin-bottom:12px;"></i>
						<?php if ($search): ?>
							<p>No records found for "<strong><?= htmlspecialchars($search) ?></strong>".</p>
						<?php else: ?>
							<p style="font-size:14px;">No SF10 records yet. <button onclick="openModal('addModal')" style="background:none;border:none;color:var(--blue);font-weight:600;cursor:pointer;font-size:14px;padding:0;">Add the first one</button></p>
						<?php endif; ?>
					</div>
				<?php else: ?>
					<table class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Student Name</th>
								<th>LRN</th>
								<th>Date of Birth</th>
								<th>Grade Level</th>
								<th>School Year</th>
								<th>Section</th>
								<th>SF10 File</th>
								<th>Notes</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($records as $r): ?>
								<tr>
									<td><strong>#<?= $r['id'] ?></strong></td>
									<td>
										<div style="display:flex;align-items:center;gap:8px;">
											<div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--purple));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;flex-shrink:0;">
												<?= strtoupper(substr($r['student_name'], 0, 1)) ?>
											</div>
											<?= htmlspecialchars($r['student_name']) ?>
										</div>
									</td>
									<td><code style="font-size:12px;background:#f1f5f9;padding:2px 6px;border-radius:4px;"><?= htmlspecialchars($r['lrn']) ?></code></td>
									<td><?= $r['date_of_birth'] ? date('M d, Y', strtotime($r['date_of_birth'])) : '—' ?></td>
									<td><?= htmlspecialchars($r['grade_level'] ?? '—') ?></td>
									<td><?= htmlspecialchars($r['school_year'] ?? '—') ?></td>
									<td><?= htmlspecialchars($r['section'] ?? '—') ?></td>
									<td>
										<?php if ($r['sf10_file']): ?>
											<a href="uploads/records/<?= urlencode($r['sf10_file']) ?>" target="_blank" class="btn btn-outline btn-sm">
												<i class="bi bi-file-earmark-arrow-down"></i> View
											</a>
										<?php else: ?>
											<span style="color:var(--text-muted);font-size:12px;">No file</span>
										<?php endif; ?>
									</td>
									<td style="max-width:160px;">
										<span style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
											<?= $r['notes'] ? htmlspecialchars($r['notes']) : '—' ?>
										</span>
									</td>
									<td>
										<div style="display:flex;gap:5px;">
											<button onclick="openEdit(<?= htmlspecialchars(json_encode($r), ENT_QUOTES) ?>)"
												class="btn btn-warning btn-sm">
												<i class="bi bi-pencil"></i>
											</button>
											<a href="?delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
												onclick="return confirm('Delete record for <?= htmlspecialchars($r['student_name']) ?>?')">
												<i class="bi bi-trash"></i>
											</a>
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

	<!-- ── ADD MODAL ─────────────────────────────────────── -->
	<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;align-items:center;justify-content:center;padding:24px;">
		<div style="background:white;border-radius:16px;width:100%;max-width:680px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lg);">
			<div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
				<h5 style="font-size:16px;font-weight:700;margin:0;"><i class="bi bi-plus-circle" style="color:var(--blue);margin-right:8px;"></i>Add SF10 Record</h5>
				<button onclick="closeModal('addModal')" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted);line-height:1;">×</button>
			</div>
			<form method="POST" enctype="multipart/form-data" style="padding:24px;">
				<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
					<div class="form-group">
						<label class="form-label">Student Name <span>*</span></label>
						<input name="student_name" type="text" class="form-control" placeholder="Full name" required>
					</div>
					<div class="form-group">
						<label class="form-label">LRN <span>*</span></label>
						<input name="lrn" type="text" class="form-control" placeholder="Learner Reference No." required>
					</div>
					<div class="form-group">
						<label class="form-label">Date of Birth</label>
						<input name="dob" type="date" class="form-control">
					</div>
					<div class="form-group">
						<label class="form-label">Grade Level</label>
						<select name="grade_level" class="form-select">
							<option value="">— Select Grade —</option>
							<?php for ($g = 1; $g <= 6; $g++) echo "<option value='Grade $g'>Grade $g</option>"; ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">School Year</label>
						<select name="school_year" class="form-select">
							<?php
							$currentYear = date("Y");
							for ($i = $currentYear; $i >= 1920; $i--) {
								$selected = ($i == $currentYear) ? 'selected' : '';
								echo "<option value='{$i}' {$selected}>{$i}</option>";
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">Section</label>
						<input name="section" type="text" class="form-control" placeholder="e.g. Mabini">
					</div>
					<div class="form-group" style="grid-column:1/-1;">
						<label class="form-label">Upload SF10 File <span style="font-weight:400;color:var(--text-muted);">(PDF, JPG, PNG, DOCX)</span></label>
						<input name="sf10_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.docx" class="form-control">
					</div>
					<div class="form-group" style="grid-column:1/-1;">
						<label class="form-label">Notes</label>
						<textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes…" style="resize:vertical;"></textarea>
					</div>
				</div>
				<div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
					<button type="button" onclick="closeModal('addModal')" class="btn btn-outline">Cancel</button>
					<button name="add" type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Record</button>
				</div>
			</form>
		</div>
	</div>

	<!-- ── Modify modal -->
	<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;align-items:center;justify-content:center;padding:24px;">
		<div style="background:white;border-radius:16px;width:100%;max-width:680px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lg);">
			<div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
				<h5 style="font-size:16px;font-weight:700;margin:0;"><i class="bi bi-pencil-square" style="color:var(--warning);margin-right:8px;"></i>Edit SF10 Record</h5>
				<button onclick="closeModal('editModal')" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted);line-height:1;">×</button>
			</div>
			<form method="POST" enctype="multipart/form-data" style="padding:24px;">
				<input type="hidden" name="edit_id" id="edit_id">
				<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
					<div class="form-group">
						<label class="form-label">Student Name <span>*</span></label>
						<input name="student_name" id="edit_student_name" type="text" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="form-label">LRN <span>*</span></label>
						<input name="lrn" id="edit_lrn" type="text" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="form-label">Date of Birth</label>
						<input name="dob" id="edit_dob" type="date" class="form-control">
					</div>
					<div class="form-group">
						<label class="form-label">Grade Level</label>
						<select name="grade_level" id="edit_grade_level" class="form-select">
							<option value="">— Select Grade —</option>
							<?php for ($g = 1; $g <= 6; $g++) echo "<option value='Grade $g'>Grade $g</option>"; ?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">School Year</label>
						<select name="school_year" id="edit_school_year" class="form-select">
							<?php
							$currentYear = date("Y");
							for ($i = $currentYear; $i >= 1920; $i--) {
								echo "<option value='{$i}'>{$i}</option>";
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label class="form-label">Section</label>
						<input name="section" id="edit_section" type="text" class="form-control">
					</div>
					<div class="form-group" style="grid-column:1/-1;">
						<label class="form-label">Replace SF10 File <span style="font-weight:400;color:var(--text-muted);">(leave blank to keep existing)</span></label>
						<div id="currentFile" style="font-size:12px;color:var(--text-muted);margin-bottom:6px;"></div>
						<input name="sf10_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.docx" class="form-control">
					</div>
					<div class="form-group" style="grid-column:1/-1;">
						<label class="form-label">Notes</label>
						<textarea name="notes" id="edit_notes" class="form-control" rows="2" style="resize:vertical;"></textarea>
					</div>
				</div>
				<div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
					<button type="button" onclick="closeModal('editModal')" class="btn btn-outline">Cancel</button>
					<button name="update" type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i> Save Changes</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		function openModal(id) {
			document.getElementById(id).style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeModal(id) {
			document.getElementById(id).style.display = 'none';
			document.body.style.overflow = '';
		}
		// Close on backdrop click
		document.querySelectorAll('[id$="Modal"]').forEach(m => {
			m.addEventListener('click', e => {
				if (e.target === m) closeModal(m.id);
			});
		});

		function openEdit(r) {
			document.getElementById('edit_id').value = r.id;
			document.getElementById('edit_student_name').value = r.student_name;
			document.getElementById('edit_lrn').value = r.lrn;
			document.getElementById('edit_dob').value = r.date_of_birth || '';
			document.getElementById('edit_school_year').value = r.school_year || '';
			document.getElementById('edit_section').value = r.section || '';
			document.getElementById('edit_notes').value = r.notes || '';
			// Grade level select
			const gradeSelect = document.getElementById('edit_grade_level');
			for (let opt of gradeSelect.options) {
				opt.selected = opt.value === r.grade_level;
			}
			// Current file
			const cf = document.getElementById('currentFile');
			cf.innerHTML = r.sf10_file ?
				`<i class="bi bi-file-earmark-check" style="color:var(--success)"></i> Current: <strong>${r.sf10_file}</strong>` :
				'No file uploaded.';
			openModal('editModal');
		}

		// Open edit modal if coming back from a GET edit
		<?php if ($editRow): ?>
			openEdit(<?= json_encode($editRow) ?>);
		<?php endif; ?>
	</script>
</body>

</html>