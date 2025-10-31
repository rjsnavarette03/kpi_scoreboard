<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: ../login.php");
	exit;
}

include('../config/db.php');
include('../includes/header.php');

/**
 * Load <option> items for a KPI category (safe/escaped).
 */
function loadOptions(mysqli $conn, string $category, $selected = null): void
{
	$sql = "SELECT score, description FROM kpi_definitions WHERE category = ? ORDER BY score DESC";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('s', $category);
	$stmt->execute();
	$res = $stmt->get_result();
	while ($row = $res->fetch_assoc()) {
		$score = (float) $row['score'];
		$desc = htmlspecialchars((string) ($row['description'] ?? ''), ENT_QUOTES, 'UTF-8');
		$sel = ($selected !== '' && $selected == $score) ? 'selected' : '';
		echo "<option value=\"{$score}\" {$sel}>{$score} - {$desc}</option>";
	}
	$stmt->close();
}

/**
 * Get a description for a given score/category from definitions.
 */
function getDesc(mysqli $conn, float $score, string $category): ?string
{
	$sql = "SELECT description FROM kpi_definitions WHERE score = ? AND category = ? LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('ds', $score, $category);
	$stmt->execute();
	$res = $stmt->get_result();
	$row = $res->fetch_assoc();
	$stmt->close();
	return $row['description'] ?? null;
}

$editing = false;
$kpi = [
	'id' => '',
	'user_id' => '',
	'productivity' => '',
	'efficiency' => '',
	'quality' => '',
	'attendance' => '',
	'tardiness' => '',
	'undertime' => ''
];

if (isset($_GET['edit'])) {
	$editing = true;
	$id = (int) $_GET['edit'];
	$stmt = $conn->prepare("SELECT * FROM kpi_scores WHERE id = ?");
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$res = $stmt->get_result();
	if ($res->num_rows > 0) {
		$kpi = $res->fetch_assoc();
	}
	$stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
	$user_id = (int) $_POST['user_id'];
	$prod = (float) $_POST['productivity'];
	$eff = (float) $_POST['efficiency'];
	$qual = (float) $_POST['quality'];
	// Schedule parts
	$attendance = (float) $_POST['attendance'];
	$tardiness = (float) $_POST['tardiness'];
	$undertime = (float) $_POST['undertime'];

	// Descriptions
	$productivity_desc = getDesc($conn, $prod, 'Productivity');
	$efficiency_desc = getDesc($conn, $eff, 'Efficiency');
	$quality_desc = getDesc($conn, $qual, 'Quality');
	$attendance_desc = getDesc($conn, $attendance, 'Attendance');
	$tardiness_desc = getDesc($conn, $tardiness, 'Tardiness');
	$undertime_desc = getDesc($conn, $undertime, 'Undertime');

	// Ensure all lookups succeeded
	foreach ([
		'Productivity' => $productivity_desc,
		'Efficiency' => $efficiency_desc,
		'Quality' => $quality_desc,
		'Attendance' => $attendance_desc,
		'Tardiness' => $tardiness_desc,
		'Undertime' => $undertime_desc,
	] as $cat => $desc) {
		if ($desc === null) {
			echo "<div class='alert alert-danger'>Invalid score selection for {$cat}. Please reselect.</div>";
			include('../includes/footer.php');
			exit;
		}
	}

	// Weighted computation
	$schedule_total = ($attendance * 0.10) + ($tardiness * 0.05) + ($undertime * 0.05); // 20%
	$total = ($prod * 0.40) + ($eff * 0.20) + ($qual * 0.20) + $schedule_total;
	$total = round($total, 2);
	$schedule_total = ($schedule_total / 20) * 100;

	// Grade bands
	if ($total >= 100) {
		$grade = 'EX';
	} elseif ($total >= 95) {
		$grade = 'EE';
	} elseif ($total >= 90) {
		$grade = 'ME';
	} elseif ($total >= 85) {
		$grade = 'NI';
	} else {
		$grade = 'UN';
	}

	// INSERT/UPDATE (prepared)
	if ($id) {
		$sql = "UPDATE kpi_scores SET 
            productivity = ?, productivity_desc = ?,
            efficiency   = ?, efficiency_desc   = ?,
            quality      = ?, quality_desc      = ?,
            attendance   = ?, attendance_desc   = ?,
            tardiness    = ?, tardiness_desc    = ?,
            undertime    = ?, undertime_desc    = ?,
            total_score  = ?, grade             = ?,
			schedule_adherence = ? WHERE id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param(
			'dsdsdsdsdsdsdsdi',
			$prod,
			$productivity_desc,
			$eff,
			$efficiency_desc,
			$qual,
			$quality_desc,
			$attendance,
			$attendance_desc,
			$tardiness,
			$tardiness_desc,
			$undertime,
			$undertime_desc,
			$total,
			$grade,
			$schedule_total,
			$id
		);
	} else {
		$sql = "INSERT INTO kpi_scores 
            (user_id, productivity, productivity_desc, efficiency, efficiency_desc, quality, quality_desc,
             attendance, attendance_desc, tardiness, tardiness_desc, undertime, undertime_desc, total_score, grade, schedule_adherence)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		// types: i d s d s d s d s d s d s d s
		$stmt->bind_param(
			'idsdsdsdsdsdsdsd',
			$user_id,
			$prod,
			$productivity_desc,
			$eff,
			$efficiency_desc,
			$qual,
			$quality_desc,
			$attendance,
			$attendance_desc,
			$tardiness,
			$tardiness_desc,
			$undertime,
			$undertime_desc,
			$total,
			$grade,
			$schedule_total
		);
	}

	if ($stmt->execute()) {
		$stmt->close();
		header("Location: dashboard.php");
		exit;
	} else {
		$err = htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
		$stmt->close();
		echo "<div class='alert alert-danger'>Error: {$err}</div>";
	}
}

// Employee dropdown logic
if ($editing) {
	$users = $conn->query("SELECT * FROM users WHERE role='employee'");
} else {
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
						<input type="hidden" name="id" value="<?= (int) $kpi['id'] ?>">
					<?php endif; ?>

					<div class="mb-3">
						<label class="form-label">Employee Name</label>
						<select name="user_id" class="form-select" <?= $editing ? 'disabled' : '' ?> required>
							<option value="">Select Employee</option>
							<?php while ($u = $users->fetch_assoc()): ?>
								<option value="<?= (int) $u['id'] ?>" <?= ($u['id'] == $kpi['user_id']) ? 'selected' : '' ?>>
									<?= htmlspecialchars((string) $u['name'], ENT_QUOTES, 'UTF-8') ?>
								</option>
							<?php endwhile; ?>
						</select>
						<?php if ($editing): ?>
							<input type="hidden" name="user_id" value="<?= (int) $kpi['user_id'] ?>">
						<?php endif; ?>
						<?php if ($users->num_rows == 0 && !$editing): ?>
							<div class="alert alert-warning mt-3">
								All employees already have KPI records assigned.
							</div>
						<?php endif; ?>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="kpi_label">Productivity (40%)</label>
							<select name="productivity" class="form-select" required>
								<option value="">Select</option>
								<?php loadOptions($conn, 'Productivity', $kpi['productivity']); ?>
							</select>
						</div>
						<div class="col-md-4 mb-3">
							<label class="kpi_label">Efficiency (20%)</label>
							<select name="efficiency" class="form-select" required>
								<option value="">Select</option>
								<?php loadOptions($conn, 'Efficiency', $kpi['efficiency']); ?>
							</select>
						</div>
						<div class="col-md-4 mb-3">
							<label class="kpi_label">Quality (20%)</label>
							<select name="quality" class="form-select" required>
								<option value="">Select</option>
								<?php loadOptions($conn, 'Quality', $kpi['quality']); ?>
							</select>
						</div>
					</div>

					<h5 class="kpi_label">Schedule Adherence (20%)</h5>
					<div class="row">
						<div class="col-md-4 mb-3">
							<label>Attendance (10%)</label>
							<select name="attendance" class="form-select" required>
								<option value="">Select</option>
								<?php loadOptions($conn, 'Attendance', $kpi['attendance']); ?>
							</select>
						</div>
						<div class="col-md-4 mb-3">
							<label>Tardiness (5%)</label>
							<select name="tardiness" class="form-select" required>
								<option value="">Select</option>
								<?php loadOptions($conn, 'Tardiness', $kpi['tardiness']); ?>
							</select>
						</div>
						<div class="col-md-4 mb-3">
							<label>Undertime (5%)</label>
							<select name="undertime" class="form-select" required>
								<option value="">Select</option>
								<?php loadOptions($conn, 'Undertime', $kpi['undertime']); ?>
							</select>
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