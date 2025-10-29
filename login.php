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

<body class="d-flex align-items-center py-4 bg-body-tertiary">
	<?php if ($error): ?>
		<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
	<?php endif; ?>
	<main class="form-signin w-100 m-auto">
		<form method="POST">
			<img class="mb-4" src="assets/images/vvs-transparent-logo.png" alt="VVS Logo" />
			<h1 class="h3 mb-3 fw-normal">Please sign in</h1>
			<div class="form-floating">
				<input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" autocomplete="off" required />
				<label for="floatingInput">Username</label>
			</div>
			<div class="form-floating">
				<input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" autocomplete="off" required />
				<label for="floatingPassword">Password</label>
			</div>
			<button class="btn btn-primary w-100 py-2" type="submit">
				Sign in
			</button>
			<p class="mt-5 mb-3 text-body-secondary">Copyrights &copy; <?php echo date('Y'); ?></p>
		</form>
	</main>
	<?php include('includes/footer.php'); ?>