<?php 
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

// Statistik dokumen
$stmt = $pdo->query("SELECT 
    COUNT(*) AS total,
    SUM(status = 'pending') AS pending,
    SUM(status = 'approved') AS approved,
    SUM(status = 'rejected') AS rejected
FROM documents");
$stats = $stmt->fetch();

// Statistik per minggu
$stats_sql = "SELECT 
    WEEK(uploaded_at, 1) - WEEK(DATE_SUB(NOW(), INTERVAL 1 MONTH), 1) + 1 AS week_num,
    COUNT(*) AS count
FROM documents
WHERE uploaded_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
GROUP BY week_num
ORDER BY week_num";
$weekly_stats = $pdo->query($stats_sql)->fetchAll();

// Dokumen terbaru
$recentDocs = $pdo->query("SELECT d.*, u.username 
                          FROM documents d 
                          JOIN users u ON d.user_id = u.id 
                          ORDER BY uploaded_at DESC 
                          LIMIT 5")->fetchAll();

// User terbaru
$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIMPELBEND</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --danger: #e74c3c;
            --success: #2ecc71;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #34495e;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: var(--primary);
            color: white;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li a {
            display: block;
            padding: 12px 20px;
            color: #ddd;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }
        
        .sidebar-menu li a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-menu li a.active {
            background: var(--secondary);
            color: white;
        }
        
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .topbar {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar-title h1 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--dark);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info .user-name {
            margin-right: 15px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .btn-logout {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-logout:hover {
            background: #c0392b;
        }
        
        .content {
            padding: 20px;
            flex: 1;
        }
        
        /* STATS */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 24px;
        }
        
        .stat-icon.total { background: rgba(52, 152, 219, 0.2); color: var(--secondary); }
        .stat-icon.pending { background: rgba(243, 156, 18, 0.2); color: var(--warning); }
        .stat-icon.approved { background: rgba(46, 204, 113, 0.2); color: var(--success); }
        .stat-icon.rejected { background: rgba(231, 76, 60, 0.2); color: var(--danger); }
        
        .stat-info h3 {
            margin: 0 0 5px;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .stat-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        /* RECENT ACTIVITY */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--dark);
        }
        
        .card-header .btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--dark);
        }
        
        table tr:hover {
            background-color: #f8f9fa;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pending { background: rgba(243, 156, 18, 0.2); color: var(--warning); }
        .status-approved { background: rgba(46, 204, 113, 0.2); color: var(--success); }
        .status-rejected { background: rgba(231, 76, 60, 0.2); color: var(--danger); }
        
        .chart-container {
            height: 300px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h3, .sidebar-menu li a span {
                display: none;
            }
            
            .sidebar-menu li a i {
                margin-right: 0;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>SIMPELBEND</h3>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> <span>Manajemen User</span></a></li>
                    <li><a href="approval.php"><i class="fas fa-check-circle"></i> <span>Persetujuan Dokumen</span></a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> <span>Laporan</span></a></li>
                    <li><a href="activity_log.php"><i class="fas fa-history"></i> <span>Riwayat Aktivitas</span></a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
        
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>Dashboard Admin</h1>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= $_SESSION['full_name'] ?? 'Admin' ?></span>
                    <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            
            <div class="content">
                <!-- STATISTIK -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['total'] ?></h3>
                            <p>Total Pengajuan</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['pending'] ?></h3>
                            <p>Menunggu Persetujuan</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon approved">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['approved'] ?></h3>
                            <p>Pengajuan Disetujui</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon rejected">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['rejected'] ?></h3>
                            <p>Pengajuan Ditolak</p>
                        </div>
                    </div>
                </div>
                
                <!-- GRAFIK STATISTIK -->
                <div class="card">
                    <div class="card-header">
                        <h2>Statistik Pengajuan Bulan Ini</h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
                
                <!-- TABEL DOKUMEN TERBARU -->
                <div class="card">
                    <div class="card-header">
                        <h2>Dokumen Terbaru</h2>
                        <a href="approval.php" class="btn">Lihat Semua</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>User</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDocs as $doc): ?>
                            <tr>
                                <td><?= $doc['id'] ?></td>
                                <td><?= htmlspecialchars($doc['title']) ?></td>
                                <td><?= htmlspecialchars($doc['username']) ?></td>
                                <td><?= date('d M Y', strtotime($doc['uploaded_at'])) ?></td>
                                <td>
                                    <span class="status status-<?= $doc['status'] ?>">
                                        <?= ucfirst($doc['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- TABEL USER TERBARU -->
                <div class="card">
                    <div class="card-header">
                        <h2>User Terbaru</h2>
                        <a href="users.php" class="btn">Lihat Semua</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= ucfirst($user['role']) ?></td>
                                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Grafik statistik bulanan
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        // Data untuk chart (dari PHP)
        const weekLabels = <?= json_encode(array_map(function($stat) { 
            return 'Minggu ' . $stat['week_num']; 
        }, $weekly_stats)) ?>;
        
        const weekData = <?= json_encode(array_column($weekly_stats, 'count')) ?>;
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: weekData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>