<?php
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

// Set default tanggal (bulan ini)
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$user_id = $_GET['user_id'] ?? '';

// Query untuk laporan
$sql = "SELECT d.*, u.username, u.full_name 
        FROM documents d
        JOIN users u ON d.user_id = u.id
        WHERE d.uploaded_at BETWEEN :start_date AND :end_date";
$params = [
    'start_date' => $start_date . ' 00:00:00',
    'end_date' => $end_date . ' 23:59:59'
];

if (!empty($user_id)) {
    $sql .= " AND d.user_id = :user_id";
    $params['user_id'] = $user_id;
}

$sql .= " ORDER BY d.uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$documents = $stmt->fetchAll();

// Ambil daftar user untuk filter
$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Dokumen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .reports-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .filter-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
            padding: 0 10px;
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn-filter {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 5px;
        }
        
        .btn-export {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    
    <div class="reports-container">
        <h2><i class="fas fa-chart-bar"></i> Laporan Dokumen</h2>
        
        <div class="filter-form">
            <h3>Filter Laporan</h3>
            <form method="get">
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" 
                               value="<?= $start_date ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" 
                               value="<?= $end_date ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <select id="user_id" name="user_id">
                            <option value="">Semua User</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                    <?= $user_id == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['full_name']) ?> (<?= $user['username'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn-filter">Terapkan Filter</button>
                <a href="#" id="exportBtn" class="btn-export"><i class="fas fa-file-export"></i> Export ke CSV</a>
            </form>
        </div>
        
        <h3>Hasil Laporan</h3>
        <?php if (empty($documents)): ?>
            <p>Tidak ada dokumen ditemukan dalam rentang waktu yang dipilih.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nomor SPM</th>
                        <th>Nama File</th>
                        <th>User</th>
                        <th>Tanggal Upload</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= $doc['id'] ?></td>
                        <td><?= htmlspecialchars($doc['title']) ?></td>
                        <td><?= htmlspecialchars($doc['original_filename']) ?></td>
                        <td><?= htmlspecialchars($doc['full_name']) ?> (<?= $doc['username'] ?>)</td>
                        <td><?= date('d M Y H:i', strtotime($doc['uploaded_at'])) ?></td>
                        <td><?= ucfirst($doc['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        // Fungsi untuk export ke CSV
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Buat data CSV
            let csv = 'ID,Judul,Nama File,User,Tanggal Upload,Status\n';
            
            <?php foreach ($documents as $doc): ?>
                csv += `<?= 
                    $doc['id'] . ',' . 
                    addslashes($doc['title']) . ',' . 
                    addslashes($doc['original_filename']) . ',' . 
                    addslashes($doc['full_name'] . ' (' . $doc['username'] . ')') . ',' . 
                    date('d M Y H:i', strtotime($doc['uploaded_at'])) . ',' . 
                    ucfirst($doc['status']) 
                ?>\n`;
            <?php endforeach; ?>
            
            // Buat blob dan download
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `laporan_dokumen_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>
</html>