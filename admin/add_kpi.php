<?php
session_start();
if ($_SESSION['role'] != 'admin') {
	header("Location: ../login.php");
	exit;
}
include('../config/db.php');
include('../includes/header.php');

$editing = false;
$kpi = ['id' => '', 'user_id' => '', 'productivity' => '', 'efficiency' => '', 'quality' => '', 'schedule_adherence' => ''];

if (isset($_GET['edit'])) {
	$editing = true;
	$id = intval($_GET['edit']);
	$res = $conn->query("SELECT * FROM kpi_scores WHERE id=$id");
	if ($res->num_rows > 0) {
		$kpi = $res->fetch_assoc();
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$id = isset($_POST['id']) ? intval($_POST['id']) : null;
	$user_id = intval($_POST['user_id']);
	$prod = floatval($_POST['productivity']);
	$eff = floatval($_POST['efficiency']);
	$qual = floatval($_POST['quality']);
	$sched = floatval($_POST['schedule_adherence']);

	// ✅ Compute total with weights
	$total = ($prod * 0.4) + ($eff * 0.2) + ($qual * 0.2) + ($sched * 0.2);

	// ✅ Compute grade based on total
	if ($total >= 100)
		$grade = 'EX';
	elseif ($total >= 95)
		$grade = 'EE';
	elseif ($total >= 90)
		$grade = 'ME';
	elseif ($total >= 85)
		$grade = 'NI';
	else
		$grade = 'UN';

	if ($id) {
		// ✅ Update existing KPI
		$sql = "UPDATE kpi_scores 
            SET productivity='$prod', efficiency='$eff', quality='$qual', 
                schedule_adherence='$sched', total_score='$total', grade='$grade' 
            WHERE id='$id'";
	} else {
		// ✅ Insert new KPI
		$sql = "INSERT INTO kpi_scores (user_id, productivity, efficiency, quality, schedule_adherence, total_score, grade)
            VALUES ('$user_id', '$prod', '$eff', '$qual', '$sched', '$total', '$grade')";
	}

	if ($conn->query($sql)) {
		header("Location: dashboard.php");
		exit;
	} else {
		echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
	}
}

$users = $conn->query("SELECT * FROM users WHERE role='employee'");
if ($editing) {
	// Show all employees if editing (to retain the one being edited)
	$users = $conn->query("SELECT * FROM users WHERE role='employee'");
} else {
	// Show only employees who don't have a KPI yet
	$users = $conn->query("
		SELECT * FROM users 
		WHERE role='employee' 
		AND id NOT IN (SELECT user_id FROM kpi_scores)
	");
}
?>

<body>
	<?php include('../includes/navbar.php'); ?>
	<div class="container-fluid">
		<div class="row min-100vh">
			<?php include('../includes/sidebar.php'); ?>
			<main class="col-md-9 ms-sm-auto col-lg-10 p-md-5 neumorph-container">
				<h2 class="mb-4"><?= $editing ? "Edit KPI" : "Add KPI" ?></h2>

				<form method="POST" class="card p-4 shadow-sm">
					<?php if ($editing): ?>
						<input type="hidden" name="id" value="<?= $kpi['id'] ?>">
					<?php endif; ?>

					<div class="mb-3">
						<label class="form-label">Employee Name</label>
						<select name="user_id" class="form-select" <?= $editing ? 'disabled' : '' ?> required>
							<option value="">Select Employee</option>
							<?php while ($u = $users->fetch_assoc()): ?>
								<option value="<?= $u['id'] ?>" <?= ($u['id'] == $kpi['user_id']) ? 'selected' : '' ?>>
									<?= htmlspecialchars($u['name']) ?>
								</option>
							<?php endwhile; ?>
						</select>
						<?php if ($users->num_rows == 0 && !$editing): ?>
							<div class="alert alert-warning mt-3">
								All employees already have KPI records assigned.
							</div>
						<?php endif; ?>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Productivity (40%)</label>
							<input type="number" step="0.01" name="productivity" class="form-control"
								value="<?= $kpi['productivity'] ?>" required>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Efficiency (20%)</label>
							<input type="number" step="0.01" name="efficiency" class="form-control"
								value="<?= $kpi['efficiency'] ?>" required>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Quality (20%)</label>
							<input type="number" step="0.01" name="quality" class="form-control"
								value="<?= $kpi['quality'] ?>" required>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Schedule Adherence (20%)</label>
							<input type="number" step="0.01" name="schedule_adherence" class="form-control"
								value="<?= $kpi['schedule_adherence'] ?>" required>
						</div>
					</div>
					<div class="container-fluid d-flex flex-row p-0 gap-3">
						<button type="submit" class="btn btn-primary"><?= $editing ? "Update" : "Save" ?></button>
						<a href="dashboard.php" class="btn btn-danger">Cancel</a>
					</div>
				</form>
			</main>
		</div>
	</div>

	<?php include('../includes/footer.php'); ?>