<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
	header('Location: /login.php');
	exit;
}
include('../config/db.php');
include('../includes/header.php');

$user_id = intval($_SESSION['user_id']);
$res = $conn->query("SELECT k.*, u.name FROM kpi_scores k JOIN users u ON k.user_id=u.id WHERE k.user_id=$user_id LIMIT 1");
$k = $res->fetch_assoc();
?>

<body>
	<?php include('../includes/navbar.php'); ?>
	<div class="container-fluid">
		<div class="row min-100vh">
			<?php include('../includes/sidebar.php'); ?>
			<main class="col-md-9 ms-sm-auto col-lg-10 p-md-5 neumorph-container">
				<h2 style="margin-bottom:2rem;">My KPI Dashboard</h2>

				<?php if ($k): ?>
					<h5 class="card-title" style="margin-bottom:2rem;"><?= htmlspecialchars($k['name']) ?></h5>
					<div class="row">
						<!-- Productivity -->
						<div class="col-xl-3 col-md-6 mb-4">
							<div class="card border border-0 h-100 py-2 neumorph">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="fs-5.6 fw-bold text-uppercase mb-1">
												Productivity</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800">
												<?= $k['productivity'] ?>&#37;
											</div>
										</div>
										<div class="col-auto">
											<i class="fas fa-chart-line fa-2x text-primary"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Efficiency -->
						<div class="col-xl-3 col-md-6 mb-4">
							<div class="card border border-0 h-100 py-2 neumorph">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="fs-5.6 fw-bold text-uppercase mb-1">
												Efficiency</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800"><?= $k['efficiency'] ?>&#37;
											</div>
										</div>
										<div class="col-auto">
											<i class="fas fa-bolt fa-2x text-warning"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Quality -->
						<div class="col-xl-3 col-md-6 mb-4">
							<div class="card border border-0 h-100 py-2 neumorph">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="fs-5.6 fw-bold text-uppercase mb-1">
												Quality</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800"><?= $k['quality'] ?>&#37;
											</div>
										</div>
										<div class="col-auto">
											<i class="fas fa-star fa-2x text-success"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Schedule Adherence -->
						<div class="col-xl-3 col-md-6 mb-4">
							<div class="card border border-0 h-100 py-2 neumorph">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="fs-5.6 fw-bold text-uppercase mb-1">
												Schedule Adherence</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800">
												<?= $k['schedule_adherence'] ?>&#37;
											</div>
										</div>
										<div class="col-auto">
											<i class="fas fa-calendar-check fa-2x text-danger"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<!-- Total Score -->
						<div class="col-xl-6 col-md-6 mb-4">
							<div class="custom-card">
								<h2><?= $k['total_score'] ?>&#37;</h2>
								<p>Total Score</p>
							</div>
						</div>
						<!-- Performance Grade -->
						<div class="col-xl-6 col-md-6 mb-4">
							<div class="custom-card">
								<div class="row z-1 flex-row w-100">
									<div class="col-xl-6 col-md-6 d-flex flex-column justify-content-center align-items-center">
										<h2><?= $k['grade'] ?></h2>
										<p>Performance Grade</p>
									</div>
									<div class="col-xl-6 col-md-6 d-flex flex-column border-start align-items-center">
										<ul class="text-white mb-0 p-0" style="width:fit-content;list-style:none;">
											<li><strong>EX</strong> - Exceptional</li>
											<li><strong>EE</strong> - Exceeds Expectations</li>
											<li><strong>ME</strong> - Meets Expectations</li>
											<li><strong>NI</strong> - Needs Improvement</li>
											<li><strong>UN</strong> - Unsatisfactory</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php else: ?>
					<div class="alert alert-info">No KPI data found for you yet. Please contact HR.</div>
				<?php endif; ?>
			</main>
		</div>
	</div>

	<?php include('../includes/footer.php'); ?>