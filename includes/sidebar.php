<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
    <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu"
        aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Virtual Ventures</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <?php if (session_status() !== PHP_SESSION_ACTIVE)
                    session_start(); ?>
                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 active" aria-current="page"
                                href="/admin/dashboard.php"><i class="fa-solid fa-house"></i>KPI Scoreboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 active" aria-current="page"
                                href="/admin/employees.php"><i class="fa-solid fa-users"></i>Employees</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2 active" aria-current="page"
                                href="/employee/dashboard.php"><i class="fa-solid fa-house"></i>My KPI</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i>Sign out
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>