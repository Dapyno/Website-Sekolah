<?php
$host = 'localhost';
$dbname = 'smp_al_islam';
$username = 'root';
$password = '';

// === ATAU GUNAKAN 127.0.0.1 ===
// $host = '127.0.0.1';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Tampilkan error lebih detail
    die("Koneksi database gagal: " . $e->getMessage());
}
?>