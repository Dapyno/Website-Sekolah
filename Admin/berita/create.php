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
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'] ?? '';
    $kategori = $_POST['kategori'] ?? 'pendidikan';
    $konten = $_POST['konten'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $gambar = '';
    
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadResult = uploadGambar($_FILES['gambar'], '../../assets/berita/');
        if($uploadResult['success']) {
            $gambar = $uploadResult['filename'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if(empty($error)) {
        $data = ['judul'=>$judul, 'kategori'=>$kategori, 'konten'=>$konten, 'gambar'=>$gambar, 'tanggal'=>$tanggal];
        if($beritaModel->create($data)) {
            header('Location: index.php?msg=Berita berhasil ditambahkan&type=success');
            exit();
        } else {
            $error = 'Gagal menambahkan berita!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita - Admin</title>
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
                <h4><i class="bi bi-plus-lg me-2"></i> Tambah Berita</h4>
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
                                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul berita" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Kategori</label>
                                        <select name="kategori" class="form-select">
                                            <option value="prestasi">🏆 Prestasi</option>
                                            <option value="acara">🎉 Acara</option>
                                            <option value="pendidikan" selected>📚 Pendidikan</option>
                                            <option value="pengumuman">📢 Pengumuman</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tanggal</label>
                                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Konten</label>
                                        <textarea name="konten" class="form-control" rows="8" placeholder="Tulis konten berita..." required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Gambar</label>
                                        <input type="file" name="gambar" class="form-control" accept="image/*">
                                        <small class="text-muted">Format: JPG, PNG, WebP. Maks 5MB</small>
                                    </div>
                                    <div class="border rounded p-3 text-center" style="min-height:150px;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                        <p class="text-muted small mt-2">Preview gambar</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Berita</button>
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