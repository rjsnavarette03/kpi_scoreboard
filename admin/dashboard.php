<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
	header('Location: /login.php');
	exit;
}
include('../config/db.php');
include('../includes/header.php');

$selected_month = isset($_GET['month']) ? trim($_GET['month']) : date('Y-m');
// validate YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
	$selected_month = date('Y-m');
}
$selected_month_date = $selected_month . '-01';

$sql = "SELECT k.*, u.name FROM kpi_scores k JOIN users u ON k.user_id = u.id WHERE k.month = '" . $conn->real_escape_string($selected_month_date) . "' ORDER BY u.username";
$res = $conn->query($sql);
?>

<body>
	<?php include('../includes/navbar.php'); ?>
	<div class="container-fluid">
		<div class="row min-100vh">
			<?php include('../includes/sidebar.php'); ?>
			<main class="col-md-9 ms-sm-auto col-lg-10 p-md-5 neumorph-container">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h2>Admin Dashboard</h2>
					<div class="d-flex gap-2">
						<form class="d-flex" method="GET">
							<input type="month" name="month" class="form-control me-2" value="<?= htmlspecialchars($selected_month, ENT_QUOTES, 'UTF-8') ?>">
							<button class="btn btn-secondary" type="submit">Filter</button>
						</form>
						<a href="add_kpi.php" class="btn btn-primary">+ Add KPI</a>
					</div>
				</div>
				<table class="table table-bordered table-hover">
					<thead class="table-dark">
						<tr>
							<th>Employee</th>
							<th>Month</th>
							<th>Productivity</th>
							<th>Efficiency</th>
							<th>Quality</th>
							<th>Schedule Adherence</th>
							<th>Total</th>
							<th>Grade</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($res && $res->num_rows > 0): ?>
							<?php while ($r = $res->fetch_assoc()): ?>
								<tr>
									<td><?= htmlspecialchars($r['name']) ?></td>
									<td><?= htmlspecialchars(date('F Y', strtotime($r['month']))) ?></td>
									<td><?= $r['productivity'] ?>%</td>
									<td><?= $r['efficiency'] ?>%</td>
									<td><?= $r['quality'] ?>%</td>
									<td><?= $r['schedule_adherence'] ?>%</td>
									<td class="table-active table-success"><?= $r['total_score'] ?>%</td>
									<td><span class="badge bg-secondary"><?= $r['grade'] ?></span></td>
									<td>
										<a href="add_kpi.php?edit=<?= $r['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
										<a href="delete_kpi.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger"
											onclick="return confirm('Delete this KPI?')">Delete</a>
									</td>
								</tr>
							<?php endwhile; ?>
						<?php else: ?>
							<tr>
								<td colspan="9" class="text-center">No KPI records yet.</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</main>
		</div>
	</div>
	<?php include('../includes/footer.php'); ?>