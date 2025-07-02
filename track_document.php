<?php
include 'config.php';
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .track-container {
            width: 100%;
            max-width: 550px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .track-header {
            background: linear-gradient(to right, #2c3e50, #1a252f);
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 50% 100%, 0 85%);
        }
        
        .logo {
            font-size: 3.5rem;
            margin-bottom: 15px;
            color: #3498db;
            text-shadow: 0 0 10px rgba(52, 152, 219, 0.5);
        }
        
        .track-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .track-header p {
            opacity: 0.8;
            font-size: 1rem;
            margin-top: 10px;
        }
        
        .track-body {
            padding: 40px 30px 30px;
            position: relative;
        }
        
        .wave-decoration {
            position: absolute;
            top: -60px;
            left: 0;
            width: 100%;
            height: 60px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z' opacity='.25' fill='%232c3e50'/%3E%3Cpath d='M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z' fill='%23ffffff'/%3E%3C/svg%3E");
            background-size: 1200px 60px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            text-align: center;
            font-size: 0.95rem;
            animation: shake 0.5s;
            position: relative;
            overflow: hidden;
        }
        
        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }
        
        .alert-error {
            background: rgba(255, 235, 238, 0.8);
            color: #c62828;
            border: 1px solid rgba(255, 205, 210, 0.5);
        }
        
        .alert-error::before {
            background: #c62828;
        }
        
        .alert-success {
            background: rgba(237, 247, 237, 0.8);
            color: #2e7d32;
            border: 1px solid rgba(200, 230, 201, 0.5);
        }
        
        .alert-success::before {
            background: #2e7d32;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            background: rgba(255, 255, 255, 0.8);
        }
        
        .input-with-icon input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            background: white;
        }
        
        .btn-track {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, #2c3e50, #1a252f);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-track::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-track:hover::before {
            left: 100%;
        }
        
        .btn-track:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.4);
        }
        
        .btn-track:active {
            transform: translateY(0);
        }
        
        .track-footer {
            text-align: center;
            padding: 20px;
            background: rgba(248, 249, 250, 0.7);
            color: #7f8c8d;
            font-size: 0.9rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .track-footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .track-footer a:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
        
        .document-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        
        .detail-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: 600;
            width: 150px;
            color: #2c3e50;
        }
        
        .detail-value {
            flex: 1;
            color: #555;
        }
        
        .status-pending {
            color: #ff9800;
            font-weight: 600;
        }
        
        .status-approved {
            color: #4caf50;
            font-weight: 600;
        }
        
        .status-rejected {
            color: #f44336;
            font-weight: 600;
        }

        /* Floating elements */
        .floating {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            backdrop-filter: blur(4px);
            z-index: -1;
        }
        
        .floating:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation: float 15s linear infinite;
        }
        
        .floating:nth-child(2) {
            width: 120px;
            height: 120px;
            bottom: 30%;
            right: 15%;
            animation: float 18s linear infinite reverse;
        }
        
        .floating:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 60%;
            left: 25%;
            animation: float 12s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, 20px) rotate(90deg); }
            50% { transform: translate(40px, 0) rotate(180deg); }
            75% { transform: translate(20px, -20px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        .results-info {
            background: rgba(52, 152, 219, 0.1);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .results-info i {
            color: #3498db;
            font-size: 1.1rem;
        }
        
        /* Responsiveness */
        @media (max-width: 480px) {
            .track-container {
                max-width: 95%;
            }
            
            .track-body {
                padding: 30px 20px;
            }
            
            .track-header {
                padding: 30px 15px;
            }
            
            .track-header h1 {
                font-size: 1.6rem;
            }
            
            .detail-item {
                flex-direction: column;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
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
            $documents = [];
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $search_term = trim($_POST['search_term'] ?? '');
                
                if (!empty($search_term)) {
                    try {
                        $sql = "SELECT 
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
                            WHERE d.id = ?
                                OR d.title LIKE ?
                                OR d.description LIKE ?
                                OR d.doc_description LIKE ?";
                        
                        $stmt = $pdo->prepare($sql);
                        
                        // Bind parameters
                        $search_id = is_numeric($search_term) ? (int)$search_term : -1;
                        $search_like = "%$search_term%";
                        $stmt->execute([
                            $search_id,
                            $search_like,
                            $search_like,
                            $search_like
                        ]);
                        
                        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($documents)) {
                            $error = 'Tidak ditemukan dokumen yang sesuai dengan kriteria pencarian.';
                        }
                    } catch (PDOException $e) {
                        $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Silakan masukkan kata kunci pencarian.';
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
                        <i class="fas fa-search"></i>
                        <input type="text" name="search_term" id="search_term" 
                               placeholder="Cari berdasarkan ID, Judul, Deskripsi..." required
                               value="<?= isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : '' ?>">
                    </div>
                </div>
                <div> </div>
                <button type="submit" class="btn-track">
                    <i class="fas fa-search"></i> LACAK STATUS
                </button>
            </form>
            
            <?php if (!empty($documents)): ?>
                <div class="results-info">
                    <i class="fas fa-info-circle"></i> Ditemukan <?= count($documents) ?> dokumen
                </div>
                
                <?php foreach ($documents as $document): ?>
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="track-footer">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>

    <script>
        // Animasi untuk container saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.track-container');
            container.style.transform = 'translateY(20px)';
            container.style.opacity = '0';
            
            setTimeout(() => {
                container.style.transition = 'transform 0.8s ease, opacity 0.8s ease';
                container.style.transform = 'translateY(0)';
                container.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>