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
    
    // ===== VALIDASI TAHUN =====
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

// ===== STATISTIK =====
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
    <title>Tambah Prestasi - Admin SMP Al Islam Krian</title>
    <link rel="icon" type="image/png" sizes="64x64" href="../../assets/logo/logo-smp-al-islam.png" />
    
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
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #F4F5F7; }
        
        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(229,231,235,0.5);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 50;
            padding: 24px 16px;
        }
        .sidebar .brand { display: flex; align-items: center; gap: 12px; padding-bottom: 24px; border-bottom: 1px solid #E5E7EB; margin-bottom: 20px; }
        .sidebar .brand img { height: 40px; width: 40px; border-radius: 10px; }
        .sidebar .brand .name { font-weight: 700; font-size: 0.95rem; color: #1F2937; }
        .sidebar .brand .tag { font-size: 0.6rem; color: #0E9F6E; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar ul li { margin-bottom: 2px; }
        .sidebar ul li a {
            display: flex; align-items: center; gap: 14px; padding: 10px 14px;
            border-radius: 10px; color: #6B7280; text-decoration: none;
            font-size: 0.85rem; font-weight: 500; transition: all 0.2s;
        }
        .sidebar ul li a:hover { background: rgba(14,159,110,0.06); color: #0E9F6E; }
        .sidebar ul li.active a { background: #0E9F6E; color: #FFFFFF; }
        .sidebar ul li a .badge {
            margin-left: auto; background: #E5E7EB; color: #6B7280;
            font-size: 0.6rem; padding: 2px 10px; border-radius: 50px;
        }
        .sidebar ul li.active a .badge { background: rgba(255,255,255,0.2); color: #FFFFFF; }
        .sidebar ul li a.text-danger { color: #EF4444; }
        .sidebar ul li a.text-danger:hover { background: rgba(239,68,68,0.08); }
        .sidebar .divider { height: 1px; background: #E5E7EB; margin: 16px 12px; }
        .sidebar .footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 16px 20px; border-top: 1px solid #E5E7EB;
            background: rgba(255,255,255,0.8);
        }
        .sidebar .footer .user { display: flex; align-items: center; gap: 12px; }
        .sidebar .footer .user .avatar {
            width: 36px; height: 36px; border-radius: 50%; background: #0E9F6E;
            display: flex; align-items: center; justify-content: center; color: #fff;
            font-weight: 700; font-size: 0.8rem;
        }
        .sidebar .footer .user .info .name { font-weight: 600; font-size: 0.85rem; color: #1F2937; }
        .sidebar .footer .user .info .role { font-size: 0.7rem; color: #6B7280; }
        
        .main-content { margin-left: 260px; padding: 24px 32px; min-height: 100vh; }
        
        .alert-danger { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        
        /* ===== FORM STYLE PREMIUM ===== */
        .form-group { margin-bottom: 20px; }
        
        .form-label {
            display: block; font-weight: 600; font-size: 0.85rem; color: #1F2937; margin-bottom: 6px;
        }
        .form-label .required { color: #EF4444; margin-left: 2px; }
        .form-label i { color: #0E9F6E; margin-right: 6px; }
        
        .form-control {
            width: 100%; padding: 12px 16px; border: 2px solid #E5E7EB; border-radius: 12px;
            font-size: 0.9rem; transition: all 0.3s; background: #FAFAFA; color: #1F2937;
            font-family: 'Poppins', sans-serif;
        }
        .form-control:focus { outline: none; border-color: #0E9F6E; background: #FFFFFF; box-shadow: 0 0 0 4px rgba(14,159,110,0.08); }
        .form-control::placeholder { color: #9CA3AF; }
        
        /* ===== INPUT TAHUN DENGAN SPINNER ===== */
        .form-control-year {
            width: 100%; padding: 12px 16px; border: 2px solid #E5E7EB; border-radius: 12px;
            font-size: 0.9rem; transition: all 0.3s; background: #FAFAFA; color: #1F2937;
            font-family: 'Poppins', sans-serif;
            -moz-appearance: textfield;
        }
        .form-control-year::-webkit-outer-spin-button,
        .form-control-year::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .form-control-year:focus { outline: none; border-color: #0E9F6E; background: #FFFFFF; box-shadow: 0 0 0 4px rgba(14,159,110,0.08); }
        
        /* ===== SELECT DROPDOWN PREMIUM ===== */
        .form-select-wrapper {
            position: relative;
        }
        .form-select-wrapper::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
            font-size: 1rem;
            pointer-events: none;
        }
        .form-select {
            width: 100%; padding: 12px 44px 12px 16px; border: 2px solid #E5E7EB; border-radius: 12px;
            font-size: 0.9rem; transition: all 0.3s; background: #FAFAFA; color: #1F2937;
            font-family: 'Poppins', sans-serif; appearance: none; cursor: pointer;
        }
        .form-select:focus { outline: none; border-color: #0E9F6E; background: #FFFFFF; box-shadow: 0 0 0 4px rgba(14,159,110,0.08); }
        .form-select option { padding: 8px; }
        
        .form-text { font-size: 0.75rem; color: #6B7280; margin-top: 4px; }
        
        .card-premium {
            background: #FFFFFF; border-radius: 20px; border: 1px solid rgba(229,231,235,0.5);
            box-shadow: 0 4px 24px rgba(0,0,0,0.04); overflow: hidden;
        }
        .card-premium .header {
            padding: 20px 28px; border-bottom: 1px solid #F1F5F9;
            background: #FAFAFA;
        }
        .card-premium .body { padding: 28px; }
        
        .upload-area {
            border: 2px dashed #E5E7EB; border-radius: 16px; padding: 32px 20px;
            text-align: center; background: #FAFAFA; transition: all 0.3s; min-height: 180px;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
        }
        .upload-area:hover { border-color: #0E9F6E; background: #F4F5F7; }
        .upload-area .icon { font-size: 3rem; color: #D1D5DB; margin-bottom: 12px; }
        .upload-area .text { color: #6B7280; font-size: 0.9rem; }
        
        .btn-smp {
            background: #0E9F6E; color: #FFFFFF; padding: 12px 28px; border-radius: 12px;
            font-weight: 600; font-size: 0.9rem; border: none; cursor: pointer;
            transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;
            font-family: 'Poppins', sans-serif;
        }
        .btn-smp:hover { background: #0B8159; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(14,159,110,0.25); }
        
        .btn-secondary-custom {
            background: #F1F5F9; color: #1F2937; padding: 12px 28px; border-radius: 12px;
            font-weight: 600; font-size: 0.9rem; border: none; cursor: pointer;
            transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;
            font-family: 'Poppins', sans-serif; text-decoration: none;
        }
        .btn-secondary-custom:hover { background: #E5E7EB; transform: translateY(-2px); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
        
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F4F5F7; }
        ::-webkit-scrollbar-thumb { background: #0E9F6E; border-radius: 10px; }
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 16px 8px; }
            .sidebar .brand .text, .sidebar ul li a span, .sidebar ul li a .badge, .sidebar .footer .user .info { display: none; }
            .sidebar .brand { justify-content: center; padding-bottom: 16px; }
            .sidebar ul li a { justify-content: center; padding: 12px; gap: 0; }
            .sidebar ul li a i { font-size: 1.2rem; }
            .main-content { margin-left: 70px; padding: 16px; }
            .sidebar .footer { padding: 12px; }
            .sidebar .footer .user { justify-content: center; }
        }
        @media (max-width: 480px) {
            .sidebar { width: 60px; padding: 12px 6px; }
            .main-content { margin-left: 60px; padding: 12px; }
            .card-premium .body { padding: 16px; }
        }
    </style>
</head>
<body>

<!-- ============================================================
SIDEBAR
============================================================ -->
<aside class="sidebar">
    <div class="brand">
        <img src="../../assets/logo/logo-smp-al-islam.png" alt="Logo" />
        <div class="text">
            <div class="name">SMP Al Islam</div>
            <div class="tag">Administrator</div>
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

<!-- ============================================================
MAIN CONTENT
============================================================ -->
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
        SMP Al Islam Krian &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

<!-- ===== JAVASCRIPT UNTUK TAHUN ===== -->
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