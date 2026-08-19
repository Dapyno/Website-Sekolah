<?php
$host = 'localhost';
$dbname = 'smp_islam_watestanjung';  // Nama database yang sudah dibuat
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Set timezone
    $pdo->exec("SET time_zone = '+07:00'");
    
} catch(PDOException $e) {
    // Tampilkan error lebih detail
    die("Koneksi database gagal: " . $e->getMessage());
}
?>