<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../../Backend/config/database.php';
require_once '../../Backend/models/Berita.php';
require_once '../../Backend/helpers/functions.php';

$beritaModel = new Berita($pdo);
$beritaList = $beritaModel->getAll();
$message = $_GET['msg'] ?? '';
$messageType = $_GET['type'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Admin</title>
    <link rel="icon" type="image/png" sizes="64x64" href="../../assets/logo/logo-smp-al-islam.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../css/admin-style.css" rel="stylesheet">
</head>

<body>
    <div class="admin-wrapper">
        <nav class="admin-sidebar">
            <div class="sidebar-brand">
                <img src="../../assets/logo/logo-smp-al-islam.png" alt="Logo" height="40">
                <span>Admin</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="../dashboard.php"><i class="bi bi-grid"></i> <span>Dashboard</span></a></li>
                <!-- ===== BERITA - ACTIVE ===== -->
                <li class="active"><a href="index.php"><i class="bi bi-newspaper"></i> <span>Berita</span></a></li>
                <li><a href="../guru/index.php"><i class="bi bi-person"></i> <span>Guru</span></a></li>
                <li><a href="../prestasi/index.php"><i class="bi bi-trophy"></i> <span>Prestasi</span></a></li>
                <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
            </ul>
        </nav>

        <main class="admin-main">
            <header class="admin-header">
                <h4><i class="bi bi-newspaper me-2"></i> Kelola Berita</h4>
                <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Berita</a>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Semua Berita (<?= count($beritaList) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Gambar</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($beritaList)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada berita. <a href="create.php">Tambahkan berita pertama</a></td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1;
                                        foreach ($beritaList as $b): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <?php if ($b['gambar']): ?>
                                                        <img src="../../assets/berita/<?= $b['gambar'] ?>" width="60" height="45" style="object-fit:cover; border-radius:6px;">
                                                    <?php else: ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= substr($b['judul'], 0, 50) ?>...</td>
                                                <td><span class="badge <?= getKategoriClass($b['kategori']) ?>"><?= getKategoriLabel($b['kategori']) ?></span></td>
                                                <td><?= formatTanggal($b['tanggal']) ?></td>
                                                <td>
                                                    <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                                    <a href="delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>