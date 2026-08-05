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
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_prestasi = $_POST['nama_prestasi'] ?? '';
    $tingkat = $_POST['tingkat'] ?? '';
    $tahun = $_POST['tahun'] ?? date('Y');
    $deskripsi = $_POST['deskripsi'] ?? '';
    $siswa = $_POST['siswa'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $gambar = '';
    
    // Upload gambar
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadResult = uploadGambar($_FILES['gambar'], '../../assets/prestasi/');
        if($uploadResult['success']) {
            $gambar = $uploadResult['filename'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if(empty($error)) {
        $data = [
            'nama_prestasi' => $nama_prestasi,
            'tingkat' => $tingkat,
            'tahun' => $tahun,
            'deskripsi' => $deskripsi,
            'gambar' => $gambar,
            'siswa' => $siswa,
            'lokasi' => $lokasi
        ];
        
        if($prestasiModel->create($data)) {
            header('Location: index.php?msg=Prestasi berhasil ditambahkan&type=success');
            exit();
        } else {
            $error = 'Gagal menambahkan prestasi!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Prestasi - Admin SMP Al Islam Krian</title>
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
                <li><a href="../guru/index.php"><i class="bi bi-person"></i> <span>Guru</span></a></li>
                <li class="active"><a href="index.php"><i class="bi bi-trophy"></i> <span>Prestasi</span></a></li>
                <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <header class="admin-header">
                <h4><i class="bi bi-plus-lg me-2"></i> Tambah Prestasi</h4>
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
                                        <label class="form-label fw-semibold">Nama Prestasi <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_prestasi" class="form-control" placeholder="Contoh: Juara 1 OSN Matematika" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tingkat <span class="text-danger">*</span></label>
                                                <select name="tingkat" class="form-select" required>
                                                    <option value="">Pilih Tingkat</option>
                                                    <option value="internasional">🌍 Internasional</option>
                                                    <option value="nasional">🇮🇩 Nasional</option>
                                                    <option value="provinsi">🏅 Provinsi</option>
                                                    <option value="kabupaten">🏆 Kabupaten</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                                <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" min="2000" max="<?= date('Y') + 1 ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tulis deskripsi prestasi..."></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Siswa / Tim</label>
                                                <input type="text" name="siswa" class="form-control" placeholder="Contoh: Ahmad Fauzi">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Lokasi</label>
                                                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jakarta">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Gambar</label>
                                        <input type="file" name="gambar" class="form-control" accept="image/*">
                                        <small class="text-muted">Format: JPG, PNG, WebP. Maks 5MB</small>
                                    </div>
                                    <div class="border rounded p-3 text-center" style="min-height:150px; background:#f8f9fa;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                        <p class="text-muted small mt-2">Preview gambar</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Prestasi</button>
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