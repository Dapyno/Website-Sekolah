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
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $mapel = $_POST['mapel'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $tahun_bergabung = $_POST['tahun_bergabung'] ?? '';
    $pendidikan = $_POST['pendidikan'] ?? '';
    $instagram = $_POST['instagram'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $email = $_POST['email'] ?? '';
    $foto = '';
    
    // Upload foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $uploadResult = uploadGambar($_FILES['foto'], '../../assets/guru/');
        if($uploadResult['success']) {
            $foto = $uploadResult['filename'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if(empty($error)) {
        $data = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'mapel' => $mapel,
            'foto' => $foto,
            'status' => $status,
            'tahun_bergabung' => $tahun_bergabung,
            'pendidikan' => $pendidikan,
            'instagram' => $instagram,
            'linkedin' => $linkedin,
            'email' => $email
        ];
        
        if($guruModel->create($data)) {
            header('Location: index.php?msg=Guru berhasil ditambahkan&type=success');
            exit();
        } else {
            $error = 'Gagal menambahkan guru!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Guru - Admin SMP Al Islam Krian</title>
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
                <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a></li>
            </ul>
        </nav>
        
        <main class="admin-main">
            <header class="admin-header">
                <h4><i class="bi bi-plus-lg me-2"></i> Tambah Guru</h4>
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
                                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                                <select name="jabatan" class="form-select" required>
                                                    <option value="">Pilih Jabatan</option>
                                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                                    <option value="Wakil Kepala Sekolah">Wakil Kepala Sekolah</option>
                                                    <option value="Guru">Guru</option>
                                                    <option value="Staff Administrasi">Staff Administrasi</option>
                                                    <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Mata Pelajaran</label>
                                                <input type="text" name="mapel" class="form-control" placeholder="Contoh: Matematika">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tahun Bergabung</label>
                                                <input type="text" name="tahun_bergabung" class="form-control" placeholder="Contoh: 2010">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Pendidikan</label>
                                                <input type="text" name="pendidikan" class="form-control" placeholder="Contoh: S2 Pendidikan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Instagram</label>
                                                <input type="text" name="instagram" class="form-control" placeholder="Username Instagram">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">LinkedIn</label>
                                                <input type="text" name="linkedin" class="form-control" placeholder="Username LinkedIn">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="email@sekolah.sch.id">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active">Aktif</option>
                                            <option value="inactive">Non-Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Foto</label>
                                        <input type="file" name="foto" class="form-control" accept="image/*">
                                        <small class="text-muted">Format: JPG, PNG, WebP. Maks 5MB</small>
                                    </div>
                                    <div class="border rounded p-3 text-center" style="min-height:200px; background:#f8f9fa;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                        <p class="text-muted small mt-2">Preview foto akan muncul di sini</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Guru</button>
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