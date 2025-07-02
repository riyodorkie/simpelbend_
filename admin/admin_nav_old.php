<nav>
    <a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="users.php" <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-users"></i> Manajemen User
    </a>
    <a href="approval.php" <?= basename($_SERVER['PHP_SELF']) == 'approval.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-check-circle"></i> Persetujuan Dokumen
    </a>
    <a href="reports.php" <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-chart-bar"></i> Laporan
    </a>
    <a href="activity_log.php" <?= basename($_SERVER['PHP_SELF']) == 'activity_log.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-history"></i> Riwayat Aktivitas
    </a>
    <a href="../logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>