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
$guruList = $guruModel->getAll();

$message = $_GET['msg'] ?? '';
$messageType = $_GET['type'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Guru - Admin SMP Al Islam Krian</title>
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
                <li><a href="../berita/index.php"><i class="bi bi-newspaper"></i> <span>Berita</span></a></li>
                <li class="active"><a href="index.php"><i class="bi bi-person"></i> <span>Guru</span></a></li>
                <li><a href="#"><i class="bi bi-trophy"></i> <span>Prestasi</span></a></li>
                <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <header class="admin-header">
                <h4><i class="bi bi-person me-2"></i> Kelola Guru</h4>
                <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Guru</a>
            </header>
            
            <div class="admin-content">
                <?php if($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Semua Guru (<?= count($guruList) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Foto</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Mapel</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($guruList)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                            Belum ada data guru. <a href="create.php">Tambahkan guru pertama</a>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php $no = 1; foreach($guruList as $g): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <?php if($g['foto'] && file_exists('../../assets/guru/' . $g['foto'])): ?>
                                            <img src="../../assets/guru/<?= $g['foto'] ?>" width="50" height="50" style="object-fit:cover; border-radius:50%;">
                                            <?php else: ?>
                                            <img src="../../assets/guru/default.jpg" width="50" height="50" style="object-fit:cover; border-radius:50%;">
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= htmlspecialchars($g['nama']) ?></strong></td>
                                        <td><?= getJabatanBadge($g['jabatan']) ?></td>
                                        <td><?= htmlspecialchars($g['mapel'] ?? '-') ?></td>
                                        <td><?= getStatusBadge($g['status']) ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <a href="delete.php?id=<?= $g['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus guru ini?')"><i class="bi bi-trash"></i></a>
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