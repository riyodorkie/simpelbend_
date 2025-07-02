<?php
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

// Ambil log aktivitas
$sql = "SELECT a.*, u.username 
        FROM activity_log a
        JOIN users u ON a.user_id = u.id
        ORDER BY created_at DESC
        LIMIT 100";
$logs = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas - Document Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container {
            padding: 20px;
        }
        
        .log-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
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
        
        .log-time {
            white-space: nowrap;
        }
        
        .log-user {
            white-space: nowrap;
        }
        
        .log-activity {
            max-width: 400px;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    
    <div class="container">
        <h1><i class="fas fa-history"></i> Riwayat Aktivitas Sistem</h1>
        
        <div class="log-container">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="log-time"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                        <td class="log-user"><?= htmlspecialchars($log['username']) ?></td>
                        <td class="log-activity"><?= htmlspecialchars($log['activity']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>