<?php
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

$user_id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}

// Proses update user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];
    
    // Jika password diisi
    if (!empty($new_password)) {
        // Validasi panjang password
        if (strlen($new_password) < 6) {
            $error = "Password minimal 6 karakter!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ?, password = ? WHERE id = ?");
            $update_stmt->execute([$full_name, $role, $hashed_password, $user_id]);
            $success = "User berhasil diperbarui!";
            
            // Log aktivitas
            logActivity($_SESSION['user_id'], "Memperbarui user: {$user['username']}");
        }
    } 
    // Update tanpa password
    else {
        $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
        $update_stmt->execute([$full_name, $role, $user_id]);
        $success = "User berhasil diperbarui!";
        
        // Log aktivitas
        logActivity($_SESSION['user_id'], "Memperbarui user: {$user['username']}");
    }
    
    // Ambil data terbaru
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn-update {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
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
    <?php include 'admin_nav.php'; ?>
    
    <div class="edit-container">
        <h2><i class="fas fa-edit"></i> Edit User: <?= htmlspecialchars($user['username']) ?></h2>
        
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
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="new_password">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-actions" style="display: flex; gap: 10px;">
                <button type="submit" class="btn-update">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="users.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</body>
</html>