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
	$username = $conn->real_escape_string($_POST['email']);
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

<body class="d-flex align-items-center py-4 bg-body-tertiary" style="justify-content: center;">
	<?php if ($error): ?>
		<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
	<?php endif; ?>
	<div class="my-container d-flex flex-column">
		<img class="mb-4" src="assets/images/vvs-transparent-logo.png" alt="VVS Logo" />
		<div class="heading">KPI Scoreboard</div>
		<form method="POST" class="form">
			<input required="" class="input" type="email" name="email" id="email" placeholder="E-mail" autocomplete="off" />
			<input required="" class="input" type="password" name="password" id="password" placeholder="Password" autocomplete="off" />
			<button class="login-button" type="submit">Sign in</button>

		</form>
		<span class="agreement"><a href="https://www.virtualventuresph.com/" target="_blank">Copyrights &copy; <?php echo date('Y'); ?></a></span>
	</div>
	<?php include('includes/footer.php'); ?>