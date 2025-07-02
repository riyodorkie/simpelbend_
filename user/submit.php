<?php 
include '../config.php';
if ($_SESSION['role'] !== 'user') header('Location: ../index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $doc_number_a = $_POST['doc_number_a'];
    $description_a = $_POST['description_a'];
    $doc_number_b = $_POST['doc_number_b'];
    $description_b = $_POST['description_b'];
    
    // Validasi kelengkapan dokumen
    $errors = [];
    if (empty($_FILES['document_a']['name'])) $errors[] = "Dokumen A wajib diunggah";
    if (empty($_FILES['document_b']['name'])) $errors[] = "Dokumen B wajib diunggah";
    if (empty($doc_number_a)) $errors[] = "Nomor Dokumen A wajib diisi";
    if (empty($doc_number_b)) $errors[] = "Nomor Dokumen B wajib diisi";
    
    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    } else {
        // Fungsi untuk upload file
        function uploadDocument($file, $title, $description, $doc_number, $doc_description, $doc_type, $user_id) {
            global $pdo;
            if ($file['error'] === UPLOAD_ERR_OK) {
                $original_name = basename($file["name"]);
                $safe_name = preg_replace("/[^a-zA-Z0-9_.-]/", "_", $original_name);
                $safe_name = time() . '_' . $doc_type . '_' . $safe_name;
                $target_dir = "../uploads/";
                $target_file = $target_dir . $safe_name;
                
                // Validasi ekstensi
                $allowed_ext = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx'];
                $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_ext)) {
                    return "Ekstensi file tidak diizinkan: $original_name";
                }
                
                // Validasi ukuran
                $max_size = 10 * 1024 * 1024; // 10MB
                if ($file['size'] > $max_size) {
                    return "Ukuran file terlalu besar: $original_name (maks 10MB)";
                }
                
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    // Simpan ke database
                    $stmt = $pdo->prepare("INSERT INTO documents 
                        (user_id, title, description, file_path, original_filename, 
                        doc_type, doc_number, doc_description) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $user_id,
                        $title . " (Dokumen $doc_type)",
                        $description, // Deskripsi umum
                        $target_file,
                        $original_name,
                        $doc_type,
                        $doc_number,
                        $doc_description
                    ]);
                    return true;
                } else {
                    return "Gagal mengupload file: $original_name";
                }
            } else {
                return "Error dalam upload file: " . $file['error'];
            }
        }
        
        // Upload dokumen A
        $result_a = uploadDocument($_FILES['document_a'], $title, $description, $doc_number_a, $description_a, 'A', $_SESSION['user_id']);
        if ($result_a !== true) {
            $error = $result_a;
        }
        
        // Upload dokumen B
        $result_b = uploadDocument($_FILES['document_b'], $title, $description, $doc_number_b, $description_b, 'B', $_SESSION['user_id']);
        if ($result_b !== true) {
            $error = $error ? $error . "<br>" . $result_b : $result_b;
        }
        
        if (empty($error)) {
            logActivity($_SESSION['user_id'], 'Mengajukan dokumen: ' . $title);
            $success = "Dokumen berhasil diajukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Dokumen - SIMPELBEND</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
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
        
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .file-input-container {
            position: relative;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        
        .file-input-container:hover {
            border-color: #3498db;
            background: #e3f2fd;
        }
        
        .file-input-container i {
            font-size: 48px;
            color: #3498db;
            margin-bottom: 15px;
        }
        
        .file-input-container p {
            margin: 10px 0;
            color: #666;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-list {
            display: none;
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .document-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }

        .document-section h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 10px;
        }
        
        .btn-submit {
            background: linear-gradient(to right, #2c3e50, #1a252f);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-submit:hover {
            background: linear-gradient(to right, #1a252f, #2c3e50);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h2><i class="fas fa-upload"></i>Pengajuan Dokumen Baru</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Judul Pengajuan</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi Umum</label>
                    <textarea id="description" name="description" placeholder="Deskripsi umum untuk kedua dokumen"></textarea>
                </div>
                
                <!-- Dokumen A -->
                <div class="document-section">
                    <h3><i class="fas fa-file-alt"></i> Dokumen A</h3>
                    
                    <div class="form-group">
                        <label for="doc_number_a">Nomor Dokumen A</label>
                        <input type="text" id="doc_number_a" name="doc_number_a" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description_a">Deskripsi Dokumen A</label>
                        <textarea id="description_a" name="description_a" placeholder="Deskripsi khusus dokumen A"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Unggah Dokumen A</label>
                        <div class="file-input-container">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik untuk memilih file Dokumen A</p>
                            <p class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG, XLSX (Maks 10MB per file)</p>
                            <input type="file" name="document_a" id="document_a" required>
                        </div>
                        <div class="file-list" id="fileListA"></div>
                    </div>
                </div>
                
                <!-- Dokumen B -->
                <div class="document-section">
                    <h3><i class="fas fa-file-alt"></i> Dokumen B</h3>
                    
                    <div class="form-group">
                        <label for="doc_number_b">Nomor Dokumen B</label>
                        <input type="text" id="doc_number_b" name="doc_number_b" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description_b">Deskripsi Dokumen B</label>
                        <textarea id="description_b" name="description_b" placeholder="Deskripsi khusus dokumen B"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Unggah Dokumen B</label>
                        <div class="file-input-container">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Klik untuk memilih file Dokumen B</p>
                            <p class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG, XLSX (Maks 10MB per file)</p>
                            <input type="file" name="document_b" id="document_b" required>
                        </div>
                        <div class="file-list" id="fileListB"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>Ajukan Dokumen
                </button>
            </form>
        </div>
    </div>

    <script>
        // Format ukuran file
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Untuk Dokumen A
        document.getElementById('document_a').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileListA');
            fileList.innerHTML = '';
            fileList.style.display = 'block';
            
            if (this.files.length > 0) {
                const file = this.files[0];
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <i class="fas fa-file"></i> 
                    ${file.name} (${formatBytes(file.size)})
                `;
                fileList.appendChild(fileItem);
            }
        });

        // Untuk Dokumen B
        document.getElementById('document_b').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileListB');
            fileList.innerHTML = '';
            fileList.style.display = 'block';
            
            if (this.files.length > 0) {
                const file = this.files[0];
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <i class="fas fa-file"></i> 
                    ${file.name} (${formatBytes(file.size)})
                `;
                fileList.appendChild(fileItem);
            }
        });
    </script>
</body>
</html>