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
    
    // VALIDASI TAHUN
    $tahunSekarang = date('Y');
    if($tahun > $tahunSekarang) {
        $error = 'Tahun tidak boleh melebihi tahun sekarang (' . $tahunSekarang . ')!';
    }
    
    if(empty($error) && isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
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

// STATISTIK
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$totalBerita = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$totalGuru = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$totalPrestasi = $stmt->fetch()['total'] ?? 0;

$tahunSekarang = date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Prestasi - Admin SMP Islam Watestanjung</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/logo/logo-smp-islam.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/logo/logo-smp-islam.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/logo/logo-smp-islam.png" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'smp': '#0E9F6E',
                        'smp-dark': '#0B8159',
                        'smp-gold': '#D4AF37',
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../css/admin-style.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/prestasi/create.css">
</head>
<body>

<!--
SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <img src="../../assets/logo/logo-smp-islam.png" alt="Logo SMP Islam Watestanjung" />
        <div class="text">
            <div class="name">SMP Islam</div>
            <div class="tag">Watestanjung</div>
        </div>
    </div>

    <ul>
        <li><a href="../dashboard.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li><a href="../berita/index.php"><i class="bi bi-newspaper"></i><span>Berita</span><span class="badge"><?= $totalBerita ?></span></a></li>
        <li><a href="../guru/index.php"><i class="bi bi-person"></i><span>Guru</span><span class="badge"><?= $totalGuru ?></span></a></li>
        <li class="active"><a href="index.php"><i class="bi bi-trophy"></i><span>Prestasi</span><span class="badge"><?= $totalPrestasi ?></span></a></li>
        <div class="divider"></div>
        <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
    </ul>

    <div class="footer">
        <div class="user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
            <div class="info">
                <div class="name"><?= $_SESSION['nama'] ?></div>
                <div class="role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<!--
MAIN CONTENT -->
<main class="main-content">

    <!-- HEADER -->
    <div class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200/50">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span class="w-10 h-10 rounded-xl bg-smp/10 text-smp flex items-center justify-center">
                    <i class="bi bi-plus-lg text-lg"></i>
                </span>
                Tambah Prestasi
            </h1>
            <p class="text-sm text-gray-400 mt-1 ml-12">Tambahkan data prestasi sekolah</p>
        </div>
        <a href="index.php" class="btn-secondary-custom">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ALERT -->
    <?php if($error): ?>
    <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fade-in alert-danger">
        <i class="bi bi-exclamation-circle text-lg"></i>
        <span class="flex-1"><?= $error ?></span>
        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- FORM CARD -->
    <div class="mt-6 card-premium">
        <div class="header flex items-center gap-3">
            <i class="bi bi-plus-circle text-smp text-xl"></i>
            <span class="font-semibold text-gray-800">Form Tambah Prestasi</span>
        </div>
        
        <div class="body">
            <form method="POST" enctype="multipart/form-data" id="formPrestasi">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-5">
                        <!-- Nama Prestasi -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-award"></i> Nama Prestasi <span class="required">*</span>
                            </label>
                            <input type="text" name="nama_prestasi" class="form-control" 
                                placeholder="Contoh: Juara 1 OSN Matematika" required />
                            <p class="form-text">Masukkan nama prestasi yang diraih</p>
                        </div>

                        <!-- Tingkat & Tahun -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-globe2"></i> Tingkat <span class="required">*</span>
                                </label>
                                <div class="form-select-wrapper">
                                    <select name="tingkat" class="form-select" required>
                                        <option value="" disabled selected>Pilih Tingkat Prestasi</option>
                                        <option value="internasional">🌍 Internasional</option>
                                        <option value="nasional">🇮🇩 Nasional</option>
                                        <option value="provinsi">🏅 Provinsi</option>
                                        <option value="kabupaten">🏆 Kabupaten</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-calendar3"></i> Tahun <span class="required">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="tahun" class="form-control-year" 
                                        value="<?= date('Y') ?>" min="2000" max="<?= $tahunSekarang ?>" 
                                        required id="tahunInput" />
                                    <div class="absolute inset-y-0 right-0 flex flex-col">
                                        <button type="button" class="flex-1 px-2 text-gray-400 hover:text-smp transition-colors" onclick="tahunUp()">
                                            <i class="bi bi-chevron-up text-xs"></i>
                                        </button>
                                        <button type="button" class="flex-1 px-2 text-gray-400 hover:text-smp transition-colors" onclick="tahunDown()">
                                            <i class="bi bi-chevron-down text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-text-paragraph"></i> Deskripsi
                            </label>
                            <textarea name="deskripsi" class="form-control" rows="4" 
                                placeholder="Tulis deskripsi prestasi..."></textarea>
                        </div>

                        <!-- Siswa & Lokasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-person"></i> Siswa / Tim
                                </label>
                                <input type="text" name="siswa" class="form-control" 
                                    placeholder="Contoh: Ahmad Fauzi" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt"></i> Lokasi
                                </label>
                                <input type="text" name="lokasi" class="form-control" 
                                    placeholder="Contoh: Jakarta" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Upload -->
                    <div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="bi bi-image"></i> Gambar
                            </label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" />
                            <p class="form-text">Format: JPG, PNG, WebP. Maks 5MB</p>
                        </div>
                        
                        <div class="upload-area">
                            <i class="bi bi-cloud-upload icon"></i>
                            <p class="text font-medium text-gray-600">Preview Gambar</p>
                            <p class="text-xs text-gray-400 mt-1">Gambar akan muncul setelah diupload</p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit" class="btn-smp">
                        <i class="bi bi-save"></i> Simpan Prestasi
                    </button>
                    <a href="index.php" class="btn-secondary-custom">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-sm text-gray-400 border-t border-gray-200/50 mt-8 pt-4">
        <i class="bi bi-shield-check text-smp"></i>
        SMP Islam Watestanjung &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

<!-- JAVASCRIPT UNTUK TAHUN -->
<script>
    const tahunInput = document.getElementById('tahunInput');
    const maxTahun = <?= $tahunSekarang ?>;
    const minTahun = 2000;

    function tahunUp() {
        let val = parseInt(tahunInput.value) || 0;
        if (val < maxTahun) {
            tahunInput.value = val + 1;
        }
    }

    function tahunDown() {
        let val = parseInt(tahunInput.value) || 0;
        if (val > minTahun) {
            tahunInput.value = val - 1;
        }
    }

    // Validasi saat input manual
    tahunInput.addEventListener('change', function() {
        let val = parseInt(this.value) || 0;
        if (val > maxTahun) {
            this.value = maxTahun;
            alert('Tahun tidak boleh melebihi ' + maxTahun + '!');
        } else if (val < minTahun) {
            this.value = minTahun;
            alert('Tahun minimal ' + minTahun + '!');
        }
    });

    // Cegah input manual yang tidak valid
    tahunInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            let val = parseInt(this.value) || 0;
            if (val > maxTahun) {
                this.value = maxTahun;
                alert('Tahun tidak boleh melebihi ' + maxTahun + '!');
            } else if (val < minTahun) {
                this.value = minTahun;
                alert('Tahun minimal ' + minTahun + '!');
            }
        }
    });
</script>

</body>
</html>