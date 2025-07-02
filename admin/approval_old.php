<?php 
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

// Filter
$user_id = $_GET['user_id'] ?? '';
$status = $_GET['status'] ?? '';

// Query dengan filter
$sql = "SELECT d.*, u.username 
        FROM documents d 
        JOIN users u ON d.user_id = u.id 
        WHERE 1=1";
        
if (!empty($user_id)) $sql .= " AND d.user_id = $user_id";
if (!empty($status)) $sql .= " AND d.status = '$status'";

$documents = $pdo->query($sql)->fetchAll();

// Approve/Reject
if (isset($_POST['action'])) {
    $doc_id = $_POST['doc_id'];
    $action = $_POST['action'];
    $notes = $_POST['notes'];
    $document_number = $_POST['document_number'] ?? null; // Ambil nomor dokumen jika ada
    
    if ($action == 'approved') {
        $stmt = $pdo->prepare("UPDATE documents 
            SET status = ?, admin_notes = ?, document_number = ?
            WHERE id = ?");
        $success = $stmt->execute([$action, $notes, $document_number, $doc_id]);
    } else {
        // Untuk status selain approved, set document_number menjadi NULL
        $stmt = $pdo->prepare("UPDATE documents 
            SET status = ?, admin_notes = ?, document_number = NULL 
            WHERE id = ?");
        $success = $stmt->execute([$action, $notes, $doc_id]);
    }
    
    if ($success) {
        // Log aktivitas
        $logMsg = "Mengupdate status dokumen ID $doc_id menjadi $action";
        if ($action == 'approved' && $document_number) {
            $logMsg .= " dengan nomor $document_number";
        }
        logActivity($_SESSION['user_id'], $logMsg);
        
        $_SESSION['success'] = "Status dokumen berhasil diperbarui!";
        header('Location: approval.php');
        exit();
    } else {
        $error = "Gagal memperbarui status dokumen";
    }
}

// Hapus dokumen
if (isset($_POST['delete_doc'])) {
    $doc_id = $_POST['doc_id'];
    
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
    if ($stmt->execute([$doc_id])) {
        // Log aktivitas
        logActivity($_SESSION['user_id'], "Menghapus dokumen ID $doc_id");
        
        $_SESSION['success'] = "Dokumen berhasil dihapus!";
        header('Location: approval.php');
        exit();
    } else {
        $error = "Gagal menghapus dokumen";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Dokumen - SIMPELBEND</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container {
            padding: 20px;
        }
        
        .filter-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        select, input[type="date"], input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
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
        
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            overflow-x: auto;
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
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
            cursor: pointer;
            border: none;
            margin-right: 3px;
        }

        .btn-action:hover {
            opacity: 0.8;
        }
        
        .btn-detail {
            background: var(--secondary);
            color: white;
        }

        .btn-approve {
            background: rgba(46, 204, 113, 0.2);
            color: var(--success);
        }

        .btn-reject {
            background: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        .btn-pending {
            background: rgba(243, 156, 18, 0.2);
            color: var(--warning);
        }
        
        .btn-delete {
            background: rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 40%;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            resize: vertical;
        }
        
        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    
    <div class="container">
        <h1><i class="fas fa-check-circle"></i> Persetujuan Dokumen</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="filter-section">
            <form method="get">
                <div class="filter-form">
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <select id="user_id" name="user_id">
                            <option value="">Semua User</option>
                            <?php 
                            $users = $pdo->query("SELECT * FROM users")->fetchAll();
                            foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $user_id == $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-filter">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
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
                        <td>
                            <a href="document_detail.php?id=<?= $doc['id'] ?>" class="btn-action btn-detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            
                            <?php if ($doc['status'] !== 'approved'): ?>
                                <button 
                                    onclick="openModal(<?= $doc['id'] ?>, 'approved')" 
                                    class="btn-action btn-approve"
                                    title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($doc['status'] !== 'rejected'): ?>
                                <button 
                                    onclick="openModal(<?= $doc['id'] ?>, 'rejected')" 
                                    class="btn-action btn-reject"
                                    title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($doc['status'] !== 'pending'): ?>
                                <button 
                                    onclick="openModal(<?= $doc['id'] ?>, 'pending')" 
                                    class="btn-action btn-pending"
                                    title="Set Pending">
                                    <i class="fas fa-clock"></i>
                                </button>
                            <?php endif; ?>
                            
                            <!-- Tombol Hapus -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                <button type="submit" name="delete_doc" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal untuk catatan dan nomor dokumen -->
    <div id="notesModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            <form method="post" id="actionForm">
                <input type="hidden" name="doc_id" id="modalDocId">
                <input type="hidden" name="action" id="modalAction">
                
                <!-- Input Nomor Dokumen (Hanya Tampil untuk Approve) -->
                <div id="docNumberField" style="display:none;">
                    <div class="form-group">
                        <label for="modalDocNumber" class="required-field">Nomor Dokumen SP2D</label>
                        <input type="text" id="modalDocNumber" name="document_number" 
                               placeholder="Masukkan Nomor SP2D Yang Sudah Terbit" required>
                    </div>
                </div>
                
                <!-- Input Catatan -->
                <div class="form-group">
                    <label for="modalNotes">Catatan (opsional)</label>
                    <textarea id="modalNotes" name="notes" rows="3" 
                              placeholder="Masukkan catatan"></textarea>
                </div>
                
                <button type="submit" class="btn-filter">Submit</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript untuk modal
        const modal = document.getElementById("notesModal");
        const span = document.getElementsByClassName("close")[0];
        
        // Fungsi untuk membuka modal
        function openModal(docId, action) {
            document.getElementById("modalDocId").value = docId;
            document.getElementById("modalAction").value = action;
            document.getElementById("modalTitle").innerText = 
                action.charAt(0).toUpperCase() + action.slice(1) + " Dokumen";
            
            // Tampilkan input nomor hanya untuk persetujuan
            const docNumberField = document.getElementById("docNumberField");
            if (action === 'approved') {
                docNumberField.style.display = 'block';
                document.getElementById("modalDocNumber").required = true;
            } else {
                docNumberField.style.display = 'none';
                document.getElementById("modalDocNumber").required = false;
            }
            
            modal.style.display = "block";
        }
        
        // Tutup modal saat klik close
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // Tutup modal saat klik di luar modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>