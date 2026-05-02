<?php
include 'db.php';

if (!isset($_GET['id'])) {
	header("Location: user_dashboard.php");
	exit();
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM requests WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
	echo "Request not found.";
	exit();
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>View Request — SF10 Request System</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
</head>

<body>

	<?php include 'partials/header.php'; ?>

	<div class="d-flex">
		<?php include 'partials/sidebar.php'; ?>
		<div class="main">
			<div class="page-header">
				<h2>Request Details</h2>
			</div>

			<div class="card-box">
				<div class="form-group">
					<label class="form-label">Student Name</label>
					<div><?php echo $data['student_name']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">LRN</label>
					<div><?php echo $data['lrn']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">Date of Birth</label>
					<div><?php echo $data['date_of_birth']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">Last School Year Attended</label>
					<div><?php echo $data['school_year']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">Purpose</label>
					<div><?php echo $data['purpose']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">Valid ID</label>
					<div>
						<?php
						$filePath = "uploads/{$data['valid_id']}";
						?>
						<?php if (!empty($filePath)): ?>
							<a href="<?php echo $filePath; ?>" target="_blank" class="btn btn-sm btn-primary">
								View File
							</a>
						<?php else: ?>
							<span class="text-muted">No file uploaded</span>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-group">
					<label class="form-label">Status</label>
					<div><?php echo $data['status']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">Delivery Method</label>
					<div><?php echo $data['delivery_method']; ?></div>
				</div>

				<div class="form-group">
					<label class="form-label">Email</label>
					<div><?php echo $data['email_delivery']; ?></div>
				</div>

				<div class="mt-4">
					<a href="javascript:history.back()" class="btn btn-outline">Back</a>
				</div>

			</div>
		</div>

</body>

</html>