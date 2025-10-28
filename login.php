<?php
session_start();
include('config/db.php');

if (isset($_SESSION['user_id'])) {
  if ($_SESSION['role'] == 'admin')
    header('Location: /admin/dashboard.php');
  else
    header('Location: /employee/dashboard.php');
  exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $conn->real_escape_string($_POST['username']);
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
  $res = $conn->query($sql);

  if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];
      if ($user['role'] == 'admin')
        header('Location: /admin/dashboard.php');
      else
        header('Location: /employee/dashboard.php');
      exit;
    } else {
      $error = 'Invalid password.';
    }
  } else {
    $error = 'User not found.';
  }
}
include('includes/header.php');
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="card-title mb-3">Login</h3>
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary">Login</button>
        </form>
        <hr>
        <p class="small text-muted">If you need to create an account, use phpMyAdmin or the generate_hash.php helper
          included.</p>
      </div>
    </div>
  </div>
</div>
<?php include('includes/footer.php'); ?>