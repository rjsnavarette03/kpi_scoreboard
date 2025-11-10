<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header('Location: /login.php');
    exit;
}
include('../config/db.php');
include('../includes/header.php');

$user_id = intval($_SESSION['user_id']);
// month filter (allow employee to view KPI for a month)
$selected_month = isset($_GET['month']) ? trim($_GET['month']) : date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
    $selected_month = date('Y-m');
}
$selected_month_date = $selected_month . '-01';

$sql = "SELECT k.*, u.name FROM kpi_scores k JOIN users u ON k.user_id=u.id WHERE k.user_id=? AND k.month = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $user_id, $selected_month_date);
$stmt->execute();
$res = $stmt->get_result();
$k = $res ? $res->fetch_assoc() : null;
$stmt->close();

// Get user's display name (fallback if $k is null)
$user_name = '';
$uStmt = $conn->prepare("SELECT name FROM users WHERE id = ? LIMIT 1");
if ($uStmt) {
    $uStmt->bind_param('i', $user_id);
    $uStmt->execute();
    $uRes = $uStmt->get_result();
    if ($uRes && $uRes->num_rows > 0) {
        $uRow = $uRes->fetch_assoc();
        $user_name = $uRow['name'];
    }
    $uStmt->close();
}
?>

<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container-fluid">
        <div class="row min-100vh">
            <?php include('../includes/sidebar.php'); ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 p-md-5 neumorph-container">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h2 style="margin-bottom:0;"><?= htmlspecialchars($user_name ?: ($k['name'] ?? '')) ?></h2>
                    <form method="GET" class="d-flex">
                        <input type="month" name="month" class="form-control me-2" value="<?= htmlspecialchars($selected_month, ENT_QUOTES, 'UTF-8') ?>">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </form>
                </div>

                <?php if ($k): ?>
                    <!-- <h5 class="card-title" style="margin-bottom:2rem;"></h5> -->
                    <div class="row">
                        <!-- Productivity -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border border-0 h-100 py-2 neumorph">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col mr-2">
                                            <div class="h1 mb-0 font-weight-bold text-gray-800">
                                                <?= $k['productivity'] ?>&#37;
                                            </div>
                                            <div class="fw-bold text-uppercase mb-1">
                                                Productivity</div>
                                        </div>
                                        <span class="col-auto" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="Test">
                                            <i class="fas fa-chart-line fa-2x text-primary"></i>
                                        </span>
                                    </div>
                                    <hr>
                                    <p><?= $k['productivity_desc'] ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Efficiency -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border border-0 h-100 py-2 neumorph">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col mr-2">
                                            <div class="h1 mb-0 font-weight-bold text-gray-800"><?= $k['efficiency'] ?>&#37;
                                            </div>
                                            <div class="fw-bold text-uppercase mb-1">
                                                Efficiency</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bolt fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                    <hr>
                                    <p><?= $k['efficiency_desc'] ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Quality -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border border-0 h-100 py-2 neumorph">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col mr-2">
                                            <div class="h1 mb-0 font-weight-bold text-gray-800"><?= $k['quality'] ?>&#37;
                                            </div>
                                            <div class="fw-bold text-uppercase mb-1">
                                                Quality</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-star fa-2x text-success"></i>
                                        </div>
                                    </div>
                                    <hr>
                                    <p><?= $k['quality_desc'] ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Schedule Adherence -->
                        <div class="col-xl-12 col-md-12 mb-4">
                            <div class="card border border-0 h-100 py-2 neumorph">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col mr-2">
                                            <div class="h1 mb-0 font-weight-bold text-gray-800">
                                                <?= $k['schedule_adherence'] ?>&#37;
                                            </div>
                                            <div class="fw-bold text-uppercase mb-1">
                                                Schedule Adherence</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-check fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <!-- Attendance -->
                                        <div class="col-xl-4 col-md-12 mb-4">
                                            <div class="card border border-0 h-100 bg-transparent">
                                                <div class="card-body border border-dark-subtle rounded-2">
                                                    <div class="row g-0 align-items-center">
                                                        <div class="col">
                                                            <div class="fw-bold mb-1">
                                                                Attendance - <?= $k['attendance'] ?>&#37;
                                                            </div>
                                                            <em class="m-0"><?= $k['attendance_desc'] ?>&#37;</em>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tardiness -->
                                        <div class="col-xl-4 col-md-12 mb-4">
                                            <div class="card border border-0 h-100 bg-transparent">
                                                <div class="card-body border border-dark-subtle rounded-2">
                                                    <div class="row g-0 align-items-center">
                                                        <div class="col">
                                                            <div class="fw-bold mb-1">
                                                                Tardiness - <?= $k['tardiness'] ?>&#37;
                                                            </div>
                                                            <em class="m-0"><?= $k['tardiness_desc'] ?>&#37;</em>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Undertime -->
                                        <div class="col-xl-4 col-md-12 mb-4">
                                            <div class="card border border-0 h-100 bg-transparent">
                                                <div class="card-body border border-dark-subtle rounded-2">
                                                    <div class="row g-0 align-items-center">
                                                        <div class="col">
                                                            <div class="fw-bold mb-1">
                                                                Undertime - <?= $k['undertime'] ?>&#37;
                                                            </div>
                                                            <em class="m-0"><?= $k['undertime_desc'] ?>&#37;</em>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Performance Grade -->
                        <div class="col-xl-12 col-md-12 mb-4">
                            <div class="custom-card">
                                <div class="row z-1 flex-row w-100">
                                    <div class="col-xl-3 col-md-6 d-flex flex-column justify-content-center align-items-center">
                                        <h2><?= $k['total_score'] ?>&#37;</h2>
                                        <p>Total Score</p>
                                    </div>
                                    <div class="col-xl-3 col-md-6 d-flex flex-column justify-content-center align-items-center border-start">
                                        <h2><?= $k['grade'] ?></h2>
                                        <p>Performance Grade</p>
                                    </div>
                                    <div class="col-xl-6 col-md-6 d-flex flex-column align-items-center border-start px-5">
                                        <table class="table table-borderless table-dark table-hover table-transparent m-0 w-75">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 40%;">100%</td>
                                                    <td style="width: 20%;">EX</td>
                                                    <td style="width: 40%;">Exceptional</td>
                                                </tr>
                                                <tr>
                                                    <td>95% - 99.99%</td>
                                                    <td>EE</td>
                                                    <td>Exceeds Expectations</td>
                                                </tr>
                                                <tr>
                                                    <td>90% - 94.99%</td>
                                                    <td>ME</td>
                                                    <td>Meets Expectations</td>
                                                </tr>
                                                <tr>
                                                    <td>85% - 89.99%</td>
                                                    <td>NI</td>
                                                    <td>Needs Improvement</td>
                                                </tr>
                                                <tr>
                                                    <td>&lt;85%</td>
                                                    <td>UN</td>
                                                    <td>Unsatisfactory</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No KPI data found for you for <?= htmlspecialchars(date('F Y', strtotime($selected_month_date)), ENT_QUOTES, 'UTF-8') ?>.</div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>