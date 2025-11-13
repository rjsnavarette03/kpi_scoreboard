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
$max_month = date('Y-m', strtotime('first day of last month'));
$selected_month = isset($_GET['month']) ? trim($_GET['month']) : $max_month;
if (!preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
    $selected_month = $max_month;
}
// Prevent selecting a future month (clamp to current month)
if ($selected_month > $max_month) {
    $selected_month = $max_month;
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
include('../includes/body-intro.php');
?>

<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0"><?= htmlspecialchars($user_name ?: ($k['name'] ?? '')) ?></h4>

                        <div class="page-title-right d-flex">
                            <form method="GET" class="d-flex">
                                <input type="month" name="month" class="form-control" value="<?= htmlspecialchars($selected_month, ENT_QUOTES, 'UTF-8') ?>" max="<?= htmlspecialchars($max_month, ENT_QUOTES, 'UTF-8') ?>">
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <?php if ($k): ?>
                <div class="row">
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Productivity</p>
                                        <h4 class="mb-2"><?= $k['productivity'] ?>&#37;</h4>
                                        <p class="text-muted mb-0"><?= $k['productivity_desc'] ?></p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-primary rounded-3">
                                            <i class="ri-bar-chart-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Efficiency</p>
                                        <h4 class="mb-2"><?= $k['efficiency'] ?>&#37;</h4>
                                        <p class="text-muted mb-0"><?= $k['efficiency_desc'] ?></p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="ri-flashlight-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Quality</p>
                                        <h4 class="mb-2"><?= $k['quality'] ?>&#37;</h4>
                                        <p class="text-muted mb-0"><?= $k['quality_desc'] ?></p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-primary rounded-3">
                                            <i class="ri-check-double-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Attendance</p>
                                        <h4 class="mb-2"><?= $k['attendance'] ?>&#37;</h4>
                                        <p class="text-muted mb-0"><?= $k['attendance_desc'] ?></p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="ri-user-follow-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Tardiness</p>
                                        <h4 class="mb-2"><?= $k['tardiness'] ?>&#37;</h4>
                                        <p class="text-muted mb-0"><?= $k['tardiness_desc'] ?></p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-primary rounded-3">
                                            <i class="ri-time-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-truncate font-size-14 mb-2">Undertime</p>
                                        <h4 class="mb-2"><?= $k['undertime'] ?>&#37;</h4>
                                        <p class="text-muted mb-0"><?= $k['undertime_desc'] ?></p>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="ri-timer-flash-line font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div><!-- end cardbody -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div><!-- end row -->

                <div class="row mt-5">
                    <div class="col-xl-12">
                        <style>
                            .premium-container {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }

                            .card_box {
                                width: 300px;
                                height: 250px;
                                border-radius: 20px;
                                background: linear-gradient(170deg, rgba(58, 56, 56, 0.623) 0%, rgb(31, 31, 31) 100%);
                                position: relative;
                                box-shadow: 0 25px 30px rgba(0, 0, 0, 0.55);
                                transition: all .3s;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-direction: column;
                            }

                            .card_box span {
                                position: absolute;
                                overflow: hidden;
                                width: 150px;
                                height: 150px;
                                top: -10px;
                                left: -10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }

                            .card_box span::before {
                                content: 'Total Score';
                                position: absolute;
                                width: 150%;
                                height: 40px;
                                background-image: linear-gradient(45deg, #ff6547 0%, #ffb144 51%, #ff7053 100%);
                                transform: rotate(-45deg) translateY(-20px);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: #fff;
                                font-weight: 600;
                                letter-spacing: 0.1em;
                                text-transform: uppercase;
                                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.23);
                            }

                            .card_box span::after {
                                content: '';
                                position: absolute;
                                width: 10px;
                                bottom: 0;
                                left: 0;
                                height: 10px;
                                z-index: -1;
                                box-shadow: 140px -140px #cc3f47;
                                background-image: linear-gradient(45deg, #FF512F 0%, #F09819 51%, #FF512F 100%);
                            }

                            .card_box .card_box_text {
                                color: #FFFFFF;
                                margin: 0;
                                font-family: Inter, sans-serif;
                                font-size: 58px;
                                line-height: 58px;
                                font-weight: 600;
                            }
                            .card_box .card_box_text.card_box_text_sm {
                                font-size: 29px;
                                line-height: 29px;
                                font-weight: 400;
                            }
                        </style>
                        <div class="premium-container">
                            <div class="card_box">
                                <span></span>
                                <p class="card_box_text mb-2"><?= $k['grade'] ?></p>
                                <p class="card_box_text card_box_text_sm"><?= $k['total_score'] ?>&#37;</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No KPI data found for you for <?= htmlspecialchars(date('F Y', strtotime($selected_month_date)), ENT_QUOTES, 'UTF-8') ?>.</div>
            <?php endif; ?>
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
</div>
<!-- end main content-->

<!-- Loading overlay (hidden by default) -->
<div id="loading-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex h-100 align-items-center justify-content-center">
        <div class="text-center text-white">
            <div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>
            <div class="mt-2">Loading…</div>
        </div>
    </div>
</div>

<!-- Auto-submit month filter and show overlay when submitting -->
<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var monthInput = document.querySelector('input[type="month"][name="month"]');
            var overlay = document.getElementById('loading-overlay');

            function showOverlay() {
                if (!overlay) return;
                overlay.classList.remove('d-none');
                // Force repaint so overlay appears before navigation
                overlay.offsetHeight;
            }

            if (monthInput && monthInput.form) {
                var form = monthInput.form;
                // Prevent double submits
                form.__submitting = false;

                form.addEventListener('submit', function(e) {
                    if (form.__submitting) {
                        // already submitting, block
                        e.preventDefault();
                        return;
                    }
                    form.__submitting = true;
                    showOverlay();
                });

                monthInput.addEventListener('change', function() {
                    // Show overlay, then submit. Small timeout helps ensure overlay renders.
                    showOverlay();
                    setTimeout(function() {
                        // Use requestSubmit if available to ensure HTML5 validation runs
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    }, 50);
                });
            }
        });
    })();
</script>

<?php include('../includes/footer.php'); ?>