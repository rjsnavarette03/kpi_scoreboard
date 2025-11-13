<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: ../login.php");
	exit;
}

include('../config/db.php');
include('../includes/header.php');

// Error/modal helpers
$error_message = '';
$show_modal = false;
$has_error = false;

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
	'month' => '',
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
	// month input comes as YYYY-MM from <input type="month">; store as DATE YYYY-MM-01
	$month_input = isset($_POST['month']) ? trim($_POST['month']) : '';
	$month = '';
	if ($month_input !== '' && preg_match('/^\d{4}-\d{2}$/', $month_input)) {
		$month = $month_input . '-01';
	} elseif ($month_input !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $month_input)) {
		// hidden field may submit a full DATE (YYYY-MM-DD) when editing — accept that
		$month = $month_input;
	} elseif ($editing && empty($month) && !empty($kpi['month'])) {
		// fallback: if editing and month wasn't in POST (disabled input), keep existing month from DB
		$month = $kpi['month'];
	}
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
	foreach (
		[
			'Productivity' => $productivity_desc,
			'Efficiency' => $efficiency_desc,
			'Quality' => $quality_desc,
			'Attendance' => $attendance_desc,
			'Tardiness' => $tardiness_desc,
			'Undertime' => $undertime_desc,
		] as $cat => $desc
	) {
		if ($desc === null) {
			$error_message = "Invalid score selection for {$cat}. Please reselect.";
			$show_modal = true;
			$has_error = true;
			break;
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

	// Prevent duplicate KPI entries for same user+month
	if ($month !== '') {
		if ($id) {
			// exclude current record id when editing
			$chk = $conn->prepare("SELECT id FROM kpi_scores WHERE user_id = ? AND month = ? AND id <> ? LIMIT 1");
			$chk->bind_param('isi', $user_id, $month, $id);
		} else {
			$chk = $conn->prepare("SELECT id FROM kpi_scores WHERE user_id = ? AND month = ? LIMIT 1");
			$chk->bind_param('is', $user_id, $month);
		}
		$chk->execute();
		$chkRes = $chk->get_result();
		if ($chkRes && $chkRes->num_rows > 0) {
			$error_message = 'This user already has a KPI for the selected month. Please choose another month or edit the existing record.';
			$show_modal = true;
			$has_error = true;
		}
		$chk->close();
	}

	// INSERT/UPDATE (prepared)
	if (!$has_error) {
		if ($id) {
			$sql = "UPDATE kpi_scores SET 
            productivity = ?, productivity_desc = ?,
            efficiency   = ?, efficiency_desc   = ?,
            quality      = ?, quality_desc      = ?,
            attendance   = ?, attendance_desc   = ?,
            tardiness    = ?, tardiness_desc    = ?,
            undertime    = ?, undertime_desc    = ?,
            total_score  = ?, grade             = ?,
			schedule_adherence = ?, month = ? WHERE id = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param(
				'dsdsdsdsdsdsdsdsi',
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
				$month,
				$id
			);
		} else {
			$sql = "INSERT INTO kpi_scores 
			(user_id, productivity, productivity_desc, efficiency, efficiency_desc, quality, quality_desc,
			 attendance, attendance_desc, tardiness, tardiness_desc, undertime, undertime_desc, total_score, grade, month, schedule_adherence)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			// types: i d s d s d s d s d s d s d s s d
			$stmt->bind_param(
				'idsdsdsdsdsdsdssd',
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
				$month,
				$schedule_total
			);
		}
	} // end if not has_error

	// Only attempt to execute when there was no validation error and a statement was prepared
	if (!$has_error) {
		if (isset($stmt) && $stmt && $stmt->execute()) {
			$stmt->close();
			header("Location: dashboard.php");
			exit;
		} else {
			$err = isset($stmt) && $stmt ? htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') : 'No statement to execute.';
			if (isset($stmt) && $stmt) {
				$stmt->close();
			}
			echo "<div class='alert alert-danger'>Error: {$err}</div>";
		}
	}
}

// Employee dropdown logic: show all employees so admin can add KPI for different months
$users = $conn->query("SELECT * FROM users WHERE role='employee'");

include('../includes/body-intro.php');
?>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0"><?= $editing ? "Edit KPI" : "Add KPI" ?></h4>
					</div>
				</div>
			</div>
			<!-- end page title -->
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-body">
							<form method="POST" class="p-4">
								<?php if ($editing): ?>
									<input type="hidden" name="id" value="<?= (int) $kpi['id'] ?>">
								<?php endif; ?>

								<div class="row">
									<div class="col-md-6 mb-3">
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
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label">Month</label>
										<input type="month" name="month" class="form-control" <?= $editing ? 'disabled' : '' ?> required value="<?= isset($kpi['month']) && $kpi['month'] !== '' ? htmlspecialchars(substr($kpi['month'], 0, 7), ENT_QUOTES, 'UTF-8') : '' ?>">
										<?php if ($editing && isset($kpi['month']) && $kpi['month'] !== ''): ?>
											<!-- disabled inputs are not submitted; include hidden field so month value is posted when editing -->
											<input type="hidden" name="month" value="<?= htmlspecialchars(substr($kpi['month'], 0, 10), ENT_QUOTES, 'UTF-8') ?>">
										<?php endif; ?>
									</div>
									<div class="col-md-12 mb-3">
										<?php if ($users->num_rows == 0 && !$editing): ?>
											<div class="alert alert-warning mt-3">
												All employees already have KPI records assigned.
											</div>
										<?php endif; ?>
									</div>
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

						</div><!-- end card -->
					</div><!-- end card -->
				</div>
			</div>
			<!-- end row -->
		</div> <!-- container-fluid -->
		<?php if ($show_modal): ?>
			<!-- Modal -->
			<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Error</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<p><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function() {
					var m = new bootstrap.Modal(document.getElementById('errorModal'));
					m.show();
				});
			</script>
		<?php endif; ?>
	</div>
	<!-- End Page-content -->
</div>
<!-- end main content-->

<?php include('../includes/footer.php'); ?>