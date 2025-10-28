<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header('Location: /login.php');
  exit;
}
include('../config/db.php');
include('../includes/header.php');

$editing = false;
$kpi = ['id' => '', 'user_id' => '', 'productivity' => '', 'efficiency' => '', 'quality' => '', 'schedule_adherence' => ''];

if (isset($_GET['edit'])) {
  $editing = true;
  $id = intval($_GET['edit']);
  $res = $conn->query("SELECT * FROM kpi_scores WHERE id=$id LIMIT 1");
  $kpi = $res->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = intval($_POST['user_id']);
  $prod = floatval($_POST['productivity']);
  $eff = floatval($_POST['efficiency']);
  $qual = floatval($_POST['quality']);
  $sched = floatval($_POST['schedule_adherence']);
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  $total = ($prod * 0.4) + ($eff * 0.2) + ($qual * 0.2) + ($sched * 0.2);
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
    $stmt = $conn->prepare("UPDATE kpi_scores SET productivity=?, efficiency=?, quality=?, schedule_adherence=?, total_score=?, grade=? WHERE id=?");
    $stmt->bind_param('ddddsdi', $prod, $eff, $qual, $sched, $total, $grade, $id);
    $stmt->execute();
    $stmt->close();
  } else {
    $stmt = $conn->prepare("INSERT INTO kpi_scores (user_id, productivity, efficiency, quality, schedule_adherence, total_score, grade) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param('idddids', $user_id, $prod, $eff, $qual, $sched, $total, $grade);
    $stmt->execute();
    $stmt->close();
  }
  header('Location: dashboard.php');
  exit;
}

$users = $conn->query("SELECT * FROM users WHERE role='employee' ORDER BY username");
?>
<h2 class="mb-3"><?= $editing ? 'Edit KPI' : 'Add KPI' ?></h2>

<form method="POST" class="card p-4 shadow-sm">
  <?php if ($editing): ?>
    <input type="hidden" name="id" value="<?= $kpi['id'] ?>">
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label">Employee</label>
    <select name="user_id" class="form-select" <?= $editing ? 'disabled' : '' ?> required>
      <option value="">-- Select employee --</option>
      <?php while ($u = $users->fetch_assoc()): ?>
        <option value="<?= $u['id'] ?>" <?= ($u['id'] == $kpi['user_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['username']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Productivity (40%)</label>
      <input type="number" step="0.01" name="productivity" class="form-control" value="<?= $kpi['productivity'] ?>"
        required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Efficiency (20%)</label>
      <input type="number" step="0.01" name="efficiency" class="form-control" value="<?= $kpi['efficiency'] ?>"
        required>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Quality (20%)</label>
      <input type="number" step="0.01" name="quality" class="form-control" value="<?= $kpi['quality'] ?>" required>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Schedule Adherence (20%)</label>
      <input type="number" step="0.01" name="schedule_adherence" class="form-control"
        value="<?= $kpi['schedule_adherence'] ?>" required>
    </div>
  </div>

  <button class="btn btn-success"><?= $editing ? 'Update' : 'Save' ?></button>
  <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include('../includes/footer.php'); ?>