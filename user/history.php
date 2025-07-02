<?php
include '../config.php';

// Redirect jika bukan user
if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil parameter pencarian
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Query dengan filter
$sql = "SELECT * FROM documents WHERE user_id = :user_id";
$params = [':user_id' => $user_id];

if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR original_filename LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status_filter) && in_array($status_filter, ['pending', 'approved', 'rejected'])) {
    $sql .= " AND status = :status";
    $params[':status'] = $status_filter;
}

$sql .= " ORDER BY uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$documents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Dokumen</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .history-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .filter-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .filter-section input, 
        .filter-section select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
            min-width: 200px;
        }
        
        .filter-section button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
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
        
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .status-pending {
            background-color: #ffecb3;
            color: #ff9800;
        }
        
        .status-approved {
            background-color: #c8e6c9;
            color: #388e3c;
        }
        
        .status-rejected {
            background-color: #ffcdd2;
            color: #d32f2f;
        }
        
        .btn-download {
            background: #3498db;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="history-container">
        <h2><i class="fas fa-history"></i> Riwayat Dokumen</h2>
        
        <div class="filter-section">
            <form method="get">
                <input type="text" name="search" placeholder="Cari judul atau nama file..." value="<?= htmlspecialchars($search) ?>">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $status_filter == 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>
        
        <?php if (empty($documents)): ?>
            <p>Belum ada dokumen yang diajukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Judul Dokumen</th>
                        <th>Nama File</th>
                        <th>Tanggal Upload</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= htmlspecialchars($doc['title']) ?></td>
                        <td><?= htmlspecialchars($doc['original_filename']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($doc['uploaded_at'])) ?></td>
                        <td>
                            <span class="status status-<?= $doc['status'] ?>">
                                <?= ucfirst($doc['status']) ?>
                            </span>
                        </td>
                        <td><?= $doc['admin_notes'] ? htmlspecialchars($doc['admin_notes']) : '-' ?></td>
                        <td>
                            <a href="../<?= htmlspecialchars($doc['file_path']) ?>" 
                               download 
                               class="btn-download"><i class="fas fa-download"></i> Download</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>