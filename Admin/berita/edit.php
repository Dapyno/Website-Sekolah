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

if(!$berita) {
    header('Location: index.php?msg=Berita tidak ditemukan&type=danger');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'] ?? '';
    $kategori = $_POST['kategori'] ?? 'pendidikan';
    $konten = $_POST['konten'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $gambar = $berita['gambar'];
    
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadResult = uploadGambar($_FILES['gambar'], '../../assets/berita/');
        if($uploadResult['success']) {
            if($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) {
                unlink('../../assets/berita/' . $berita['gambar']);
            }
            $gambar = $uploadResult['filename'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if(empty($error)) {
        $data = ['judul'=>$judul, 'kategori'=>$kategori, 'konten'=>$konten, 'gambar'=>$gambar, 'tanggal'=>$tanggal];
        if($beritaModel->update($id, $data)) {
            header('Location: index.php?msg=Berita berhasil diperbarui&type=success');
            exit();
        } else {
            $error = 'Gagal memperbarui berita!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - Admin</title>
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
                <li class="active"><a href="index.php"><i class="bi bi-newspaper"></i> <span>Berita</span></a></li>
                <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <header class="admin-header">
                <h4><i class="bi bi-pencil me-2"></i> Edit Berita</h4>
                <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </header>
            
            <div class="admin-content">
                <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Judul Berita</label>
                                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($berita['judul']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Kategori</label>
                                        <select name="kategori" class="form-select">
                                            <option value="prestasi" <?= $berita['kategori']=='prestasi'?'selected':'' ?>>🏆 Prestasi</option>
                                            <option value="acara" <?= $berita['kategori']=='acara'?'selected':'' ?>>🎉 Acara</option>
                                            <option value="pendidikan" <?= $berita['kategori']=='pendidikan'?'selected':'' ?>>📚 Pendidikan</option>
                                            <option value="pengumuman" <?= $berita['kategori']=='pengumuman'?'selected':'' ?>>📢 Pengumuman</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control" value="<?= $berita['tanggal'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Konten</label>
                                        <textarea name="konten" class="form-control" rows="8" required><?= htmlspecialchars($berita['konten']) ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Gambar</label>
                                        <input type="file" name="gambar" class="form-control" accept="image/*">
                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
                                    </div>
                                    <div class="border rounded p-3 text-center" style="min-height:150px;">
                                        <?php if($berita['gambar']): ?>
                                        <img src="../../assets/berita/<?= $berita['gambar'] ?>" class="img-fluid rounded" style="max-height:150px;">
                                        <?php else: ?>
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                        <p class="text-muted small mt-2">Tidak ada gambar</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Update Berita</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>