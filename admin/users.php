<?php
include '../config.php';
if ($_SESSION['role'] !== 'admin') header('Location: ../index.php');

$error = '';
$success = '';

// Tambah user baru
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validasi input
    if (empty($username) || empty($password) || empty($full_name)) {
        $error = "Semua field harus diisi!";
    } 
    // Validasi panjang password
    elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } 
    // Cek username sudah ada
    else {
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $check_stmt->execute([$username]);
        $user_exists = $check_stmt->fetchColumn();
        
        if ($user_exists) {
            $error = "Username sudah digunakan!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) 
                                         VALUES (?, ?, ?, ?)");
            $insert_stmt->execute([$username, $hashed_password, $full_name, $role]);
            $success = "User berhasil ditambahkan!";
            
            // Log aktivitas
            logActivity($_SESSION['user_id'], "Menambah user baru: $username");
        }
    }
}

// Hapus user
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Cek apakah mencoba menghapus diri sendiri
    if ($user_id == $_SESSION['user_id']) {
        $error = "Tidak dapat menghapus akun sendiri!";
    } 
    // Hapus user
    else {
        $delete_stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->execute([$user_id]);
        $success = "User berhasil dihapus!";
        
        // Log aktivitas
        logActivity($_SESSION['user_id'], "Menghapus user ID: $user_id");
    }
}

// Ambil semua user
$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>USERS - SIMPELBEND</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .users-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .user-form {
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
        
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
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
        
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        
        .btn-add {
            background: #2ecc71;
            color: white;
            border: none;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>
    
    <div class="users-container">
        <h2><i class="fas fa-users"></i> Manajemen User</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="user-form">
            <h3>Tambah User Baru</h3>
            <form method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="user" selected>User</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="add_user" class="btn btn-add">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </form>
        </div>
        
        <h3>Daftar User</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= ucfirst($user['role']) ?></td>
                    <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="users.php?delete=<?= $user['id'] ?>" 
                           class="btn btn-delete" 
                           onclick="return confirm('Yakin ingin menghapus user ini?')">
                           <i class="fas fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>