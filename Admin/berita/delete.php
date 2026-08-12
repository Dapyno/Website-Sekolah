<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../../Backend/config/database.php';
require_once '../../Backend/models/Berita.php';

$beritaModel = new Berita($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0) {
    header('Location: index.php?msg=ID berita tidak valid&type=danger');
    exit();
}

$berita = $beritaModel->getById($id);

if(!$berita) {
    header('Location: index.php?msg=Berita tidak ditemukan&type=danger');
    exit();
}

if(!empty($berita['gambar']) && $berita['gambar'] != 'default.jpg') {
    $filePath = '../../assets/berita/' . $berita['gambar'];
    if(file_exists($filePath)) {
        unlink($filePath);
    }
}

if($beritaModel->delete($id)) {
    header('Location: index.php?msg=Berita berhasil dihapus&type=success');
} else {
    header('Location: index.php?msg=Gagal menghapus berita&type=danger');
}
exit();
?>