<?php
include '../config.php';

// Redirect jika bukan user
if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password jika ingin mengubah
    if (!empty($new_password)) {
        // Verifikasi password saat ini
        if (!password_verify($current_password, $user['password'])) {
            $error = "Password saat ini salah!";
        } 
        // Validasi password baru
        elseif ($new_password !== $confirm_password) {
            $error = "Password baru dan konfirmasi tidak cocok!";
        } 
        // Validasi panjang password
        elseif (strlen($new_password) < 6) {
            $error = "Password minimal 6 karakter!";
        } 
        // Update password
        else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, password = ? WHERE id = ?");
            $update_stmt->execute([$full_name, $hashed_password, $user_id]);
            $success = "Profil berhasil diperbarui!";
        }
    } 
    // Update tanpa password
    else {
        $update_stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $update_stmt->execute([$full_name, $user_id]);
        $success = "Profil berhasil diperbarui!";
    }
    
    // Ambil data terbaru setelah update
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn-update {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        
        .btn-update:hover {
            background: #2980b9;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    
    <div class="profile-container">
        <h2><i class="fas fa-user"></i> Profil Pengguna</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="current_password">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" 
                       placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>
            
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" id="new_password" name="new_password" 
                       placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>
            
            <button type="submit" class="btn-update"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>