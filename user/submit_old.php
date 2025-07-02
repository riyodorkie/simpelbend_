<?php 
include '../config.php';
if ($_SESSION['role'] !== 'user') header('Location: ../index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    // Proses multi-file upload
    if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
        $total_files = count($_FILES['documents']['name']);
        $upload_success = true;
        
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['documents']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['documents']['name'][$i],
                    'tmp_name' => $_FILES['documents']['tmp_name'][$i],
                    'size' => $_FILES['documents']['size'][$i]
                ];
                
                // Generate nama file yang aman
                $original_name = basename($file["name"]);
                $safe_name = preg_replace("/[^a-zA-Z0-9_.-]/", "_", $original_name);
                $safe_name = time() . '_' . $i . '_' . $safe_name;
                
                // Upload file
                $target_dir = "../uploads/";
                $target_file = $target_dir . $safe_name;
                
                // Validasi ekstensi file
                $allowed_ext = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx'];
                $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed_ext)) {
                    $error = "Ekstensi file tidak diizinkan: $original_name";
                    $upload_success = false;
                    break;
                }
                
                // Validasi ukuran file (maks 10MB)
                $max_size = 10 * 1024 * 1024;
                if ($file['size'] > $max_size) {
                    $error = "Ukuran file terlalu besar: $original_name (maks 10MB)";
                    $upload_success = false;
                    break;
                }
                
                if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    // Simpan ke database
                    $stmt = $pdo->prepare("INSERT INTO documents 
                        (user_id, title, description, file_path, original_filename) 
                        VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_SESSION['user_id'],
                        $title . " (Dokumen " . ($i+1) . ")",
                        $description,
                        $target_file,
                        $original_name
                    ]);
                    
                    // Log aktivitas
                    logActivity($_SESSION['user_id'], 'Mengajukan dokumen: ' . $title);
                } else {
                    $error = "Gagal mengupload file: $original_name";
                    $upload_success = false;
                    break;
                }
            }
        }
        
        if ($upload_success) {
            $success = "Dokumen berhasil diajukan!";
        }
    } else {
        $error = "Silakan pilih file dokumen";
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
                    <label for="title">Judul Dokumen</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" placeholder="Deskripsikan dokumen yang diajukan"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Unggah Dokumen</label>
                    <div class="file-input-container">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik untuk memilih file atau tarik file ke sini</p>
                        <p class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG, XLSX (Maks 10MB per file)</p>
                        <input type="file" name="documents[]" id="documents" multiple required>
                    </div>
                    <div class="file-list" id="fileList"></div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>Ajukan Dokumen
                </button>
            </form>
        </div>
    </div>

    <script>
        // Tampilkan daftar file yang dipilih
        document.getElementById('documents').addEventListener('change', function(e) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';
            fileList.style.display = 'block';
            
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <i class="fas fa-file"></i> 
                    ${file.name} (${formatBytes(file.size)})
                `;
                fileList.appendChild(fileItem);
            }
        });
        
        // Format ukuran file
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    </script>
</body>
</html>