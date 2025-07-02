<?php
// config.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doc_management"; // Ganti dengan nama database Anda

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to the database!";
$conn->close();
?>