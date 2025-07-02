<?php
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

$doc_id = $_GET['id'] ?? 0;

// Ambil data dokumen
$stmt = $pdo->prepare("SELECT d.*, u.username, u.full_name 
                      FROM documents d
                      JOIN users u ON d.user_id = u.id
                      WHERE d.id = ?");
$stmt->execute([$doc_id]);
$document = $stmt->fetch();

if (!$document) {
    header('Location: approval.php');
    exit();
}

// Proses approve/reject
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $notes = $_POST['notes'];
    
    $update_stmt = $pdo->prepare("UPDATE documents 
        SET status = ?, admin_notes = ? 
        WHERE id = ?");
    if ($update_stmt->execute([$action, $notes, $doc_id])) {
        // Log aktivitas
        logActivity($_SESSION['user_id'], "Mengupdate status dokumen ID $doc_id menjadi $action");
        
        $_SESSION['success'] = "Status dokumen berhasil diperbarui!";
        header('Location: approval.php');
        exit();
    } else {
        $error = "Gagal memperbarui status dokumen";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Dokumen - SIMPELBEND</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container {
            padding: 20px;
        }
        
        .document-details {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #555;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-pending { background: rgba(243, 156, 18, 0.2); color: #f39c12; }
        .status-approved { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .status-rejected { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        
        .btn-download {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        
        .btn-download i {
            font-size: 1.1rem;
        }
        
        .approval-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            cursor: pointer;
            border: none;
        }
        
        .btn-update {
            background: var(--success);
            color: black;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    
    <div class="container">
        <h1><i class="fas fa-file-alt"></i> Detail Dokumen</h1>
        
        <?php if ($error): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="document-details">
            <div class="detail-item">
                <div class="detail-label">ID Dokumen</div>
                <div class="detail-value"><?= $document['id'] ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Judul Dokumen</div>
                <div class="detail-value"><?= htmlspecialchars($document['title']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Deskripsi</div>
                <div class="detail-value"><?= htmlspecialchars($document['description']) ?: '-' ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">User</div>
                <div class="detail-value">
                    <?= htmlspecialchars($document['full_name']) ?> (<?= $document['username'] ?>)
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Nama File</div>
                <div class="detail-value"><?= htmlspecialchars($document['original_filename']) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Tanggal Upload</div>
                <div class="detail-value"><?= date('d M Y H:i', strtotime($document['uploaded_at'])) ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="status status-<?= $document['status'] ?>">
                        <?= ucfirst($document['status']) ?>
                    </span>
                </div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Catatan Admin</div>
                <div class="detail-value"><?= $document['admin_notes'] ? htmlspecialchars($document['admin_notes']) : '-' ?></div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label">Aksi</div>
                <div class="detail-value">
                    <a href="../<?= $document['file_path'] ?>" download class="btn-download">
                        <i class="fas fa-download"></i> Download Dokumen
                    </a>
                </div>
            </div>
        </div>
        
        <div class="approval-form">
            <h2><i class="fas fa-edit"></i> Update Status Dokumen</h2>
            <form method="post">
                <div class="form-group">
                    <label for="action">Status</label>
                    <select id="action" name="action" required>
                        <option value="pending" <?= $document['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $document['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $document['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea id="notes" name="notes" placeholder="Berikan catatan untuk user..."><?= $document['admin_notes'] ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-update">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                    <a href="approval.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>