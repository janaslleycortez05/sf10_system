<?php
include 'db.php';
if (!isset($_SESSION['user'])) {
	header("Location: login.php");
	exit;
}
if ($_SESSION['user']['role'] === 'admin') {
	header("Location: admin_dashboard.php");
	exit;
}

$user_id = $_SESSION['user']['id'];
$success = '';
$error   = '';

// Fetch latest user data
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if (isset($_POST['update_profile'])) {
	$name      = $conn->real_escape_string(trim($_POST['name']));
	$email     = $conn->real_escape_string(trim($_POST['email']));
	$phone     = $conn->real_escape_string(trim($_POST['phone']));
	$address   = $conn->real_escape_string(trim($_POST['address']));
	$dob       = $conn->real_escape_string($_POST['dob']);
	$guardian  = $conn->real_escape_string(trim($_POST['guardian_name']));
	$guard_rel = $conn->real_escape_string(trim($_POST['guardian_relation']));
	$guard_ph  = $conn->real_escape_string(trim($_POST['guardian_phone']));

	// Check email not taken by another user
	$check = $conn->query("SELECT id FROM users WHERE email='$email' AND id != $user_id");
	$conn->query("UPDATE users SET
      name='$name', email='$email', phone='$phone', address='$address',
      date_of_birth='$dob', guardian_name='$guardian',
      guardian_relation='$guard_rel', guardian_phone='$guard_ph'
      WHERE id=$user_id");
	// Refresh session
	$_SESSION['user'] = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
	$user = $_SESSION['user'];
	$success = "Profile updated successfully.";
}

// Change Password
if (isset($_POST['change_password'])) {
	$current = $_POST['current_password'];
	$new     = $_POST['new_password'];
	$confirm = $_POST['confirm_password'];

	if ($current !== $user['password']) {
		$error = "Current password is incorrect.";
	} elseif (strlen($new) < 6) {
		$error = "New password must be at least 6 characters.";
	} elseif ($new !== $confirm) {
		$error = "New passwords do not match.";
	} else {
		$new_esc = $conn->real_escape_string($new);
		$conn->query("UPDATE users SET password='$new_esc' WHERE id=$user_id");
		$_SESSION['user']['password'] = $new;
		$user['password'] = $new;
		$success = "Password changed successfully.";
	}
}

$initials = strtoupper(substr($user['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Profile — SF10 Request System</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
	<style>
		.tab-nav {
			display: flex;
			gap: 4px;
			margin-bottom: 24px;
			border-bottom: 2px solid var(--border);
		}

		.tab-btn {
			padding: 10px 18px;
			font-size: 13.5px;
			font-weight: 600;
			border: none;
			background: none;
			color: var(--text-muted);
			cursor: pointer;
			border-bottom: 2px solid transparent;
			margin-bottom: -2px;
			transition: var(--transition);
			font-family: inherit;
			display: flex;
			align-items: center;
			gap: 6px;
		}

		.tab-btn:hover {
			color: var(--text);
		}

		.tab-btn.active {
			color: var(--blue);
			border-bottom-color: var(--blue);
		}

		.tab-pane {
			display: none;
		}

		.tab-pane.active {
			display: block;
		}

		.avatar-large {
			width: 80px;
			height: 80px;
			border-radius: 50%;
			background: linear-gradient(135deg, var(--blue), var(--purple));
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 30px;
			font-weight: 800;
			color: white;
			flex-shrink: 0;
		}

		.section-label {
			font-size: 11px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .08em;
			color: var(--text-muted);
			margin-bottom: 14px;
			margin-top: 24px;
			padding-bottom: 6px;
			border-bottom: 1px solid var(--border);
		}

		.section-label:first-child {
			margin-top: 0;
		}
	</style>
</head>

<body>

	<?php include 'partials/header.php'; ?>

	<div class="d-flex">
		<?php include 'partials/sidebar.php'; ?>

		<main class="main">
			<div class="page-header">
				<h2>My Profile</h2>
				<div class="breadcrumb"><a href="user_dashboard.php">Home</a> / Profile</div>
			</div>

			<!-- Profile Header Card -->
			<div class="card-box mb-4" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
				<div class="avatar-large"><?= $initials ?></div>
				<div style="flex:1;">
					<div style="font-size:20px;font-weight:800;color:var(--text);letter-spacing:-.02em;">
						<?= htmlspecialchars($user['name']) ?>
					</div>
					<div style="font-size:13px;color:var(--text-muted);margin-top:3px;">
						<i class="bi bi-envelope" style="margin-right:4px;"></i><?= htmlspecialchars($user['email']) ?>
					</div>
					<?php if (!empty($user['phone'])): ?>
						<div style="font-size:13px;color:var(--text-muted);margin-top:2px;">
							<i class="bi bi-telephone" style="margin-right:4px;"></i><?= htmlspecialchars($user['phone']) ?>
						</div>
					<?php endif; ?>
				</div>
				<span class="badge" style="background:#dbeafe;color:#1e40af;align-self:flex-start;">
					<i class="bi bi-person" style="font-size:10px;"></i> Student
				</span>
			</div>

			<?php if ($success): ?>
				<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px;">
					<i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
				</div>
			<?php endif; ?>
			<?php if ($error): ?>
				<div class="alert-error" style="margin-bottom:20px;">
					<i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
				</div>
			<?php endif; ?>

			<!-- Tabs -->
			<div class="tab-nav">
				<button class="tab-btn active" onclick="switchTab('personal', this)">
					<i class="bi bi-person-lines-fill"></i> Personal Info
				</button>
				<button class="tab-btn" onclick="switchTab('guardian', this)">
					<i class="bi bi-people-fill"></i> Guardian / Parent
				</button>
				<button class="tab-btn" onclick="switchTab('security', this)">
					<i class="bi bi-shield-lock-fill"></i> Security
				</button>
			</div>

			<!-- Tab: Personal Info -->
			<div id="tab-personal" class="tab-pane active" style="display:block;">
				<div class="card-box">
					<form method="POST">
						<div class="section-label">Basic Information</div>
						<div class="form-row">
							<div class="form-group">
								<label class="form-label">Full Name <span>*</span></label>
								<input name="name" type="text" class="form-control"
									value="<?= htmlspecialchars($user['name']) ?>" required>
							</div>
							<div class="form-group" style="max-width: 50%;">
								<label class="form-label">Date of Birth</label>
								<input name="dob" type="date" class="form-control"
									value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
							</div>
						</div>

						<div class="section-label">Contact Information</div>
						<div class="form-row">
							<div class="form-group">
								<label class="form-label">Email Address <span>*</span></label>
								<div class="input-icon-wrap">
									<i class="bi bi-envelope"></i>
									<input name="email" type="email" class="form-control"
										value="<?= htmlspecialchars($user['email']) ?>" required>
								</div>
							</div>
							<div class="form-group">
								<label class="form-label">Phone Number</label>
								<div class="input-icon-wrap">
									<i class="bi bi-telephone"></i>
									<input name="phone" type="tel" class="form-control"
										placeholder="e.g. 09171234567"
										value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
								</div>
							</div>
						</div>

						<div class="form-group" style="max-width: 80%;">
							<label class="form-label">Home Address</label>
							<div class="input-icon-wrap">
								<i class="bi bi-geo-alt"></i>
								<input name="address" type="text" class="form-control"
									placeholder="e.g. Brgy. Silangan Lazaan Nagcarlan, Laguna"
									value="<?= htmlspecialchars($user['address'] ?? '') ?>">
							</div>
						</div>

						<!-- Hidden guardian fields to preserve on submit -->
						<input type="hidden" name="guardian_name" value="<?= htmlspecialchars($user['guardian_name'] ?? '') ?>">
						<input type="hidden" name="guardian_relation" value="<?= htmlspecialchars($user['guardian_relation'] ?? '') ?>">
						<input type="hidden" name="guardian_phone" value="<?= htmlspecialchars($user['guardian_phone'] ?? '') ?>">

						<div style="display:flex;justify-content:flex-end;margin-top:8px;">
							<button name="update_profile" type="submit" class="btn btn-primary">
								<i class="bi bi-floppy"></i> Save Changes
							</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Tab: Guardian -->
			<div id="tab-guardian" class="tab-pane" style="display:none;">
				<div class="card-box">
					<form method="POST">
						<div class="section-label">Guardian / Parent Information</div>
						<div class="form-row">
							<div class="form-group">
								<label class="form-label">Guardian / Parent Name</label>
								<div class="input-icon-wrap">
									<i class="bi bi-person"></i>
									<input name="guardian_name" type="text" class="form-control"
										placeholder="e.g. Maria Dela Cruz"
										value="<?= htmlspecialchars($user['guardian_name'] ?? '') ?>">
								</div>
							</div>
							<div class="form-group" style="max-width: 50%;">
								<label class="form-label">Relationship</label>
								<select name="guardian_relation" class="form-select">
									<option value="">— Select —</option>
									<?php
									$relations = ['Mother', 'Father', 'Guardian', 'Grandmother', 'Grandfather', 'Aunt', 'Uncle', 'Sibling', 'Other'];
									foreach ($relations as $rel) {
										$sel = ($user['guardian_relation'] ?? '') === $rel ? 'selected' : '';
										echo "<option value='$rel' $sel>$rel</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group" style="max-width: 40%;">
							<label class="form-label">Guardian Phone Number</label>
							<div class="input-icon-wrap">
								<i class="bi bi-telephone"></i>
								<input name="guardian_phone" type="tel" class="form-control"
									placeholder="e.g. 09181234567"
									value="<?= htmlspecialchars($user['guardian_phone'] ?? '') ?>">
							</div>
						</div>

						<!-- Hidden basic fields to preserve on submit -->
						<input type="hidden" name="name" value="<?= htmlspecialchars($user['name']) ?>">
						<input type="hidden" name="email" value="<?= htmlspecialchars($user['email']) ?>">
						<input type="hidden" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
						<input type="hidden" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
						<input type="hidden" name="dob" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">

						<div style="display:flex;justify-content:flex-end;margin-top:8px;">
							<button name="update_profile" type="submit" class="btn btn-primary">
								<i class="bi bi-floppy"></i> Save Changes
							</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Tab: Security -->
			<div id="tab-security" class="tab-pane" style="display:none;">
				<div class="card-box">
					<div class="section-label">Change Password</div>
					<form method="POST">
						<div class="form-group" style="max-width: 50%;">
							<label class="form-label">Current Password <span>*</span></label>
							<div class="input-icon-wrap">
								<i class="bi bi-lock"></i>
								<input name="current_password" type="password" class="form-control"
									placeholder="Enter current password" required>
							</div>
						</div>
						<div class="form-group" style="max-width: 50%;">
							<label class="form-label">New Password <span>*</span></label>
							<div class="input-icon-wrap">
								<i class="bi bi-lock-fill"></i>
								<input name="new_password" type="password" class="form-control"
									placeholder="At least 6 characters" required>
							</div>
						</div>
						<div class="form-group" style="max-width: 50%;">
							<label class="form-label">Confirm New Password <span>*</span></label>
							<div class="input-icon-wrap">
								<i class="bi bi-lock-fill"></i>
								<input name="confirm_password" type="password" class="form-control"
									placeholder="Repeat new password" required>
							</div>
						</div>
						<div style="display:flex;justify-content:flex-end;margin-top:8px;">
							<button name="change_password" type="submit" class="btn btn-primary">
								<i class="bi bi-shield-check"></i> Update Password
							</button>
						</div>
					</form>
				</div>
			</div>

		</main>
	</div>

	<script>
		function switchTab(name, el) {
			document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
			document.getElementById('tab-' + name).style.display = 'block';
			document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
			el.classList.add('active');
		}

		<?php if ($success === 'Password changed successfully.' || (!empty($error) && isset($_POST['change_password']))): ?>
			document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
			document.getElementById('tab-security').style.display = 'block';
			document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
			document.querySelectorAll('.tab-btn')[2].classList.add('active');
		<?php endif; ?>
	</script>
</body>

</html>