<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}
include('../config/db.php');
include('../includes/header.php');

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: employees.php");
    exit;
}

// Handle add or edit
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($_POST['id'])) {
        // Update existing user
        $id = $_POST['id'];
        if (!empty($_POST['password'])) {
            $sql = "UPDATE users SET name='$name', username='$username', role='$role', password='$password' WHERE id=$id";
        } else {
            $sql = "UPDATE users SET name='$name', username='$username', role='$role' WHERE id=$id";
        }
        $conn->query($sql);
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $username, $password, $role);
        $stmt->execute();
    }

    header("Location: employees.php");
    exit;
}

// Fetch users
$result = $conn->query("SELECT * FROM users WHERE role = 'employee' ORDER BY id DESC");
?>

<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container-fluid">
        <div class="row min-100vh">
            <?php include('../includes/sidebar.php'); ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 p-md-5">
                <h2 class="mb-4">Manage Employees</h2>

                <!-- Add/Edit Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" id="user_id">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Full Name" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="email" name="username" id="username" class="form-control" placeholder="Email Address"
                                        required>
                                </div>
                                <div class="col-md-2">
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Password">
                                </div>
                                <div class="col-md-2">
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="employee">Employee</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" name="save" class="btn btn-primary w-100">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Employee Table -->
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= ucfirst($row['role']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning editBtn" data-id="<?= $row['id']; ?>"
                                        data-name="<?= htmlspecialchars($row['name']); ?>"
                                        data-username="<?= htmlspecialchars($row['username']); ?>"
                                        data-role="<?= $row['role']; ?>">Edit</button>
                                    <a href="?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
    <script>
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelector('#user_id').value = btn.dataset.id;
                document.querySelector('#name').value = btn.dataset.name;
                document.querySelector('#username').value = btn.dataset.username;
                document.querySelector('#role').value = btn.dataset.role;
                document.querySelector('#password').value = '';
            });
        });
    </script>
    <?php include('../includes/footer.php'); ?>