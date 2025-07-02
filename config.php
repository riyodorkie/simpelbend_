<?php
// Cek status session sebelum memulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1'; // Ganti IP Komputer untuk di upload
$db   = 'doc_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fungsi log aktivitas
function logActivity($user_id, $activity) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, activity) VALUES (?, ?)");
    $stmt->execute([$user_id, $activity]);
}
?>