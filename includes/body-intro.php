<?php
// Ensure session is started once so $_SESSION is available.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Determine display name: prefer fullname, then username, then DB lookup via user_id.
$displayName = 'Guest';
if (!empty($_SESSION['fullname'])) {
    $displayName = $_SESSION['fullname'];
} elseif (!empty($_SESSION['username'])) {
    $displayName = $_SESSION['username'];
} elseif (!empty($_SESSION['user_id'])) {
    // Defensive DB lookup only if $conn (mysqli) is available
    $user_id = (int) $_SESSION['user_id'];
    if ($user_id > 0 && isset($conn) && $conn instanceof mysqli) {
        $uStmt = $conn->prepare("SELECT name, username FROM users WHERE id = ? LIMIT 1");
        if ($uStmt) {
            $uStmt->bind_param('i', $user_id);
            $uStmt->execute();
            $uRes = $uStmt->get_result();
            if ($uRes && $uRes->num_rows > 0) {
                $uRow = $uRes->fetch_assoc();
                if (!empty($uRow['name'])) {
                    $displayName = $uRow['name'];
                } elseif (!empty($uRow['username'])) {
                    $displayName = $uRow['username'];
                }
            }
            $uStmt->close();
        }
    }
}

// Extract the first word (Unicode-safe). Fallback to original if splitting fails.
$firstWord = $displayName;
if (is_string($displayName) && trim($displayName) !== '') {
    $parts = preg_split('/\s+/u', trim($displayName));
    if ($parts !== false && count($parts) > 0 && $parts[0] !== '') {
        $firstWord = $parts[0];
    }
}

// Escape for safe HTML output
$displayName = htmlspecialchars($firstWord, ENT_QUOTES, 'UTF-8');
?>

<body data-topbar="dark">
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="#" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="/assets/images/vvs-transparent-logo.png" alt="logo-sm-light" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets/images/vvs-transparent-logo.png" alt="logo-light" height="50">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                        id="vertical-menu-btn">
                        <i class="ri-menu-2-line align-middle"></i>
                    </button>

                </div>

                <div class="d-flex">
                    <div class="dropdown d-inline-block user-dropdown">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="/assets/images/avatar-1.jpg"
                                alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1"><?php echo $displayName; ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item text-danger" href="/logout.php"><i
                                    class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!-- User details -->
                <div class="user-profile text-center mt-3">
                    <div class="">
                        <img src="/assets/images/avatar-1.jpg" alt="" class="avatar-md rounded-circle">
                    </div>
                    <div class="mt-3">
                        <h4 class="font-size-16 mb-1"><?php echo $displayName; ?></h4>
                        <span class="text-muted"><i
                                class="ri-record-circle-line align-middle font-size-14 text-success"></i> Online</span>
                    </div>
                </div>

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <?php // session already started above 
                        ?>
                        <?php if (isset($_SESSION['role'])): ?>
                            <li class="menu-title">Menu</li>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <li>
                                    <a href="/admin/dashboard.php" class=" waves-effect">
                                        <i class="ri-home-line"></i>
                                        <span>KPI Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/admin/employees.php" class=" waves-effect">
                                        <i class="ri-user-2-line"></i>
                                        <span>Employees</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="/employee/dashboard.php" class=" waves-effect">
                                        <i class="ri-home-line"></i>
                                        <span>My KPI</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="/legend.php" class=" waves-effect">
                                    <i class="ri-file-info-line"></i>
                                    <span>Score Guide</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->