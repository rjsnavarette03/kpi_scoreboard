<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
  header('Location: /login.php');
  exit;
}
include('../config/db.php');
include('../includes/header.php');

$user_id = intval($_SESSION['user_id']);
$res = $conn->query("SELECT k.*, u.username FROM kpi_scores k JOIN users u ON k.user_id=u.id WHERE k.user_id=$user_id LIMIT 1");
$k = $res->fetch_assoc();
?>

<body>
  <?php include('../includes/navbar.php'); ?>
  <h2>My KPI Dashboard</h2>

  <?php if ($k): ?>
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($k['username']) ?></h5>
        <div class="row">
          <div class="col-md-6">
            <p><strong>Productivity:</strong> <?= $k['productivity'] ?></p>
            <p><strong>Efficiency:</strong> <?= $k['efficiency'] ?></p>
          </div>
          <div class="col-md-6">
            <p><strong>Quality:</strong> <?= $k['quality'] ?></p>
            <p><strong>Schedule Adherence:</strong> <?= $k['schedule_adherence'] ?></p>
          </div>
        </div>
        <hr>
        <p><strong>Total Score:</strong> <?= $k['total_score'] ?></p>
        <p><strong>Performance Grade:</strong> <?= $k['grade'] ?></p>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-info">No KPI data found for you yet. Please contact HR.</div>
  <?php endif; ?>

  <?php include('../includes/footer.php'); ?>