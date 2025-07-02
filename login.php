<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validasi input
    if (empty($username) || empty($password)) {
        header('Location: index.php?error=1');
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $users = $stmt->fetch();
    
    if ($user && password_verify($password, $users['password'])) {
        $_SESSION['user_id'] = $users['id'];
        $_SESSION['username'] = $users['username'];
        $_SESSION['role'] = $users['role'];
        $_SESSION['full_name'] = $users['full_name'];
        
        // Log aktivitas
        logActivity($users['id'], 'User login');
        
        if ($users['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: user/dashboard.php');
        }
        exit();
    } else {
        header('Location: index.php?error=1');
        exit();
    }
} else {
    // Jika bukan POST request, redirect ke halaman login
    header('Location: index.php');
    exit();
}
?>