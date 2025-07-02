<style>
/* Warna Dasar */
:root {
    --primary: #2c3e50;
    --secondary: #3498db;
    --hover: #2980b9;
    --active: #1abc9c;
    --text: #ecf0f1;
    --background: #34495e;
}

/* Style Navigasi */
nav {
    background-color: var(--primary);
    padding: 15px 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

nav a {
    display: block;
    color: var(--text);
    text-decoration: none;
    padding: 12px 20px;
    margin: 5px 10px;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

nav a i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
}

/* Hover Effect */
nav a:hover {
    background-color: var(--hover);
    transform: translateX(5px);
}

/* Active Link */
nav a.active {
    background-color: var(--active);
    font-weight: bold;
    box-shadow: 0 0 10px rgba(26, 188, 156, 0.5);
}

/* Logout Button */
nav a[href="../logout.php"] {
    background-color: #e74c3c;
    margin-top: 20px;
}

nav a[href="../logout.php"]:hover {
    background-color: #c0392b;
}

/* Responsive Design */
@media (max-width: 768px) {
    nav {
        padding: 10px 0;
    }
    nav a {
        padding: 10px 15px;
        margin: 3px 5px;
        font-size: 14px;
    }
}
</style>
<nav>
    <a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="submit.php" <?= basename($_SERVER['PHP_SELF']) == 'submit.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-upload"></i> Pengajuan Dokumen
    </a>
    <a href="history.php" <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-history"></i> Riwayat
    </a>
    <a href="profile.php" <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'class="active"' : '' ?>>
        <i class="fas fa-user"></i> Profil
    </a>
    <a href="../logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>