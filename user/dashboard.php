<?php 
include '../config.php';
if ($_SESSION['role'] !== 'user') header('Location: ../index.php');

$user_id = $_SESSION['user_id'];

// Hitung status dokumen
$stmt = $pdo->prepare("SELECT 
    SUM(status = 'pending') AS pending,
    SUM(status = 'approved') AS approved,
    SUM(status = 'rejected') AS rejected
FROM documents WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

// Ambil dokumen terbaru
$recentDocs = $pdo->prepare("SELECT * FROM documents 
                            WHERE user_id = ? 
                            ORDER BY uploaded_at DESC 
                            LIMIT 5");
$recentDocs->execute([$user_id]);
$documents = $recentDocs->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Document Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .welcome-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .welcome-section h1 {
            margin: 0;
            color: var(--dark);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        
        .recent-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-header h2 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--dark);
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
        
        .btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>Selamat datang, <?= $_SESSION['full_name'] ?>!</h1>
            <p>Berikut ringkasan aktivitas Anda dalam sistem</p>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['pending'] ?? 0 ?></h3>
                    <p>Menunggu Persetujuan</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon approved">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['approved'] ?? 0 ?></h3>
                    <p>Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon rejected">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stats['rejected'] ?? 0 ?></h3>
                    <p>Ditolak</p>
                </div>
            </div>
        </div>
        
        <div class="recent-section">
            <div class="section-header">
                <h2>Dokumen Terbaru</h2>
                <a href="history.php" class="btn">Lihat Semua</a>
            </div>
            
            <?php if (empty($documents)): ?>
                <p>Belum ada dokumen yang diajukan.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['title']) ?></td>
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
            <?php endif; ?>
        </div>
    </div>
</body>
</html>