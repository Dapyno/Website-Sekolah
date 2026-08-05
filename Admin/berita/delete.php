<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../../Backend/config/database.php';
require_once '../../Backend/models/Berita.php';
require_once '../../Backend/helpers/functions.php';

$beritaModel = new Berita($pdo);
$id = $_GET['id'] ?? 0;
$berita = $beritaModel->getById($id);

if($berita) {
    if($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) {
        unlink('../../assets/berita/' . $berita['gambar']);
    }
    $beritaModel->delete($id);
    header('Location: index.php?msg=Berita berhasil dihapus&type=success');
} else {
    header('Location: index.php?msg=Berita tidak ditemukan&type=danger');
}
exit();
?>