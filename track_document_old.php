<?php
include 'config.php'; // Pastikan file config.php sudah menginisialisasi $pdo
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Dokumen - SIMPELBEND</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ... (CSS tetap sama) ... */
    </style>
</head>
<body>
    <!-- Floating background elements -->
    <div class="floating"></div>
    <div class="floating"></div>
    <div class="floating"></div>
    
    <div class="track-container">
        <div class="track-header">
            <div class="logo">
                <i class="fas fa-search"></i>
            </div>
            <h1>LACAK DOKUMEN</h1>
            <p>SIMPELBEND - KABUPATEN KOTAWARINGIN BARAT</p>
        </div>
        
        <div class="wave-decoration"></div>
        
        <div class="track-body">
            <?php
            $error = '';
            $document = null;
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $doc_id = $_POST['doc_id'] ?? '';
                
                if (!empty($doc_id)) {
                    try {
                        // Cari dokumen berdasarkan ID
                        $stmt = $pdo->prepare("SELECT 
                                d.id, 
                                d.title, 
                                d.description AS general_desc,
                                d.status, 
                                d.admin_notes, 
                                d.uploaded_at, 
                                d.doc_type,
                                d.doc_number,
                                d.doc_description,
                                d.document_number AS sp2d_number,
                                u.full_name AS user_name 
                            FROM documents d 
                            JOIN users u ON d.user_id = u.id 
                            WHERE d.id = ?");
                        $stmt->execute([$doc_id]);
                        $document = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!$document) {
                            $error = 'Dokumen dengan nomor tersebut tidak ditemukan.';
                        }
                    } catch (PDOException $e) {
                        $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Silakan masukkan nomor dokumen.';
                }
            }
            ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-file-alt"></i>
                        <input type="text" name="doc_id" id="doc_id" placeholder="Masukkan Nomor Dokumen" required
                               value="<?= isset($_POST['doc_id']) ? htmlspecialchars($_POST['doc_id']) : '' ?>">
                    </div>
                </div>
                
                <button type="submit" class="btn-track">
                    <i class="fas fa-search"></i> LACAK STATUS
                </button>
            </form>
            
            <?php if ($document): ?>
                <div class="document-details">
                    <div class="detail-item">
                        <div class="detail-label">Nomor Dokumen</div>
                        <div class="detail-value"><?= htmlspecialchars($document['id']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Tipe Dokumen</div>
                        <div class="detail-value"><?= htmlspecialchars($document['doc_type']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Nomor Dokumen</div>
                        <div class="detail-value"><?= htmlspecialchars($document['doc_number']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Judul Dokumen</div>
                        <div class="detail-value"><?= htmlspecialchars($document['title']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Deskripsi Umum</div>
                        <div class="detail-value"><?= htmlspecialchars($document['general_desc']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Deskripsi Khusus</div>
                        <div class="detail-value"><?= htmlspecialchars($document['doc_description']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <?php 
                            $status_class = 'status-' . $document['status'];
                            $status_text = '';
                            
                            switch ($document['status']) {
                                case 'pending':
                                    $status_text = 'Menunggu Persetujuan';
                                    break;
                                case 'approved':
                                    $status_text = 'Disetujui';
                                    break;
                                case 'rejected':
                                    $status_text = 'Ditolak';
                                    break;
                                default:
                                    $status_text = ucfirst($document['status']);
                            }
                            ?>
                            <span class="<?= $status_class ?>">
                                <i class="fas 
                                    <?= $document['status'] === 'approved' ? 'fa-check-circle' : 
                                       ($document['status'] === 'rejected' ? 'fa-times-circle' : 'fa-clock') ?>">
                                </i> <?= $status_text ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($document['sp2d_number'] && $document['status'] === 'approved'): ?>
                    <div class="detail-item">
                        <div class="detail-label">Nomor SP2D</div>
                        <div class="detail-value"><?= htmlspecialchars($document['sp2d_number']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($document['admin_notes']): ?>
                    <div class="detail-item">
                        <div class="detail-label">Catatan Admin</div>
                        <div class="detail-value"><?= htmlspecialchars($document['admin_notes']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-item">
                        <div class="detail-label">Diupload Oleh</div>
                        <div class="detail-value"><?= htmlspecialchars($document['user_name']) ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Tanggal Upload</div>
                        <div class="detail-value">
                            <?= date('d M Y H:i', strtotime($document['uploaded_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="track-footer">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>

    <script>
        // ... (JavaScript tetap sama) ...
    </script>
</body>
</html>