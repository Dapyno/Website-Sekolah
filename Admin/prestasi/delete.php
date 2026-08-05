<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../../Backend/config/database.php';
require_once '../../Backend/models/Prestasi.php';
require_once '../../Backend/helpers/functions.php';

$prestasiModel = new Prestasi($pdo);
$id = $_GET['id'] ?? 0;
$prestasi = $prestasiModel->getById($id);

if($prestasi) {
    // Hapus gambar jika ada dan bukan default
    if($prestasi['gambar'] && file_exists('../../assets/prestasi/' . $prestasi['gambar']) && $prestasi['gambar'] != 'default.jpg') {
        unlink('../../assets/prestasi/' . $prestasi['gambar']);
    }
    $prestasiModel->delete($id);
    header('Location: index.php?msg=Prestasi berhasil dihapus&type=success');
} else {
    header('Location: index.php?msg=Prestasi tidak ditemukan&type=danger');
}
exit();
?>