<?php
// Konfigurasi database
$host = "localhost";      // Server database
$user = "root";           // Username database
$password = "";           // Password database
$database = "doc_management"; // Nama database
$table = "users";         // Nama tabel yang diuji

// 1. Uji Koneksi ke Server Database
$conn = new mysqli($host, $user, $password);

// Cek koneksi level server
if ($conn->connect_error) {
    die("Koneksi ke SERVER database gagal: " . $conn->connect_error);
}
echo "1. Berhasil terkoneksi ke server database!<br>";

// 2. Uji Koneksi ke Database 'doc_management'
if (!$conn->select_db($database)) {
    die("2. Koneksi ke DATABASE gagal: " . $conn->error);
}
echo "2. Berhasil terkoneksi ke database <b>'$database'</b>!<br>";

// 3. Uji Keberadaan Tabel 'users'
$checkTable = $conn->query("SHOW TABLES LIKE '$table'");

if ($checkTable->num_rows == 0) {
    die("3. ERROR: Tabel <b>'$table'</b> tidak ditemukan dalam database");
}
echo "3. Tabel <b>'$table'</b> ditemukan dalam database!<br>";

// 4. Uji Struktur Tabel (Opsional)
$structure = $conn->query("DESCRIBE $table");
echo "4. Struktur tabel '$table':<br>";
echo "<ul>";
while ($column = $structure->fetch_assoc()) {
    echo "<li><b>{$column['Field']}</b> - {$column['Type']}</li>";
}
echo "</ul>";

// 5. Uji Koneksi Data (Ambil 1 record)
$testData = $conn->query("SELECT * FROM $table LIMIT 1");
if ($testData->num_rows > 0) {
    $data = $testData->fetch_assoc();
    echo "5. Data pertama ditemukan: ";
    print_r($data);
} else {
    echo "5. Peringatan: Tabel ditemukan tapi tidak berisi data";
}

// Tutup koneksi
$conn->close();
?>