<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../../Backend/config/database.php';
require_once '../../Backend/models/Guru.php';
require_once '../../Backend/helpers/functions.php';

$guruModel = new Guru($pdo);
$id = $_GET['id'] ?? 0;
$guru = $guruModel->getById($id);

if($guru) {
    // Hapus foto jika ada
    if($guru['foto'] && file_exists('../../assets/guru/' . $guru['foto'])) {
        unlink('../../assets/guru/' . $guru['foto']);
    }
    $guruModel->delete($id);
    header('Location: index.php?msg=Guru berhasil dihapus&type=success');
} else {
    header('Location: index.php?msg=Guru tidak ditemukan&type=danger');
}
exit();
?>