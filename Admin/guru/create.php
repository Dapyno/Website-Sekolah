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

// ===== AMBIL DATA STATISTIK =====
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$totalBerita = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$totalGuru = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$totalPrestasi = $stmt->fetch()['total'] ?? 0;

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
    
    // ===== VALIDASI TAHUN BERGABUNG =====
    $tahunSekarang = date('Y');
    if(!empty($tahun_bergabung)) {
        // Cek apakah tahun adalah angka dan tidak melebihi tahun sekarang
        if(!is_numeric($tahun_bergabung)) {
            $error = 'Tahun bergabung harus berupa angka!';
        } elseif((int)$tahun_bergabung > $tahunSekarang) {
            $error = 'Tahun bergabung tidak boleh melebihi tahun ' . $tahunSekarang . ' (tahun berjalan)!';
        } elseif((int)$tahun_bergabung < 1990) {
            $error = 'Tahun bergabung minimal tahun 1990!';
        }
    } else {
        // Jika kosong, set default ke tahun sekarang
        $tahun_bergabung = $tahunSekarang;
    }
    
    // Upload foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0 && empty($error)) {
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
    
    <!-- ===== TAILWIND CSS ===== -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ===== TAILWIND CONFIG ===== -->
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
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #F4F5F7; }
        
        /* Sidebar */
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
        
        .sidebar-brand { display: flex; align-items: center; gap: 12px; padding-bottom: 24px; border-bottom: 1px solid #E5E7EB; margin-bottom: 20px; }
        .sidebar-brand img { height: 40px; width: 40px; border-radius: 10px; object-fit: cover; }
        .sidebar-brand .name { font-weight: 700; font-size: 0.95rem; color: #1F2937; }
        .sidebar-brand .tag { font-size: 0.6rem; color: #0E9F6E; text-transform: uppercase; letter-spacing: 1px; }
        
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { margin-bottom: 2px; }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #6B7280;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-menu li a:hover { background: rgba(14,159,110,0.06); color: #0E9F6E; }
        .sidebar-menu li.active a { background: #0E9F6E; color: #FFFFFF; }
        .sidebar-menu li a .badge {
            margin-left: auto;
            background: #E5E7EB;
            color: #6B7280;
            font-size: 0.6rem;
            padding: 2px 10px;
            border-radius: 50px;
        }
        .sidebar-menu li.active a .badge { background: rgba(255,255,255,0.2); color: #FFFFFF; }
        .sidebar-menu li a.text-danger { color: #EF4444; }
        .sidebar-menu li a.text-danger:hover { background: rgba(239,68,68,0.08); }
        
        .sidebar-divider { height: 1px; background: #E5E7EB; margin: 16px 12px; }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px 20px;
            border-top: 1px solid #E5E7EB;
            background: rgba(255,255,255,0.8);
        }
        .sidebar-footer .user { display: flex; align-items: center; gap: 12px; }
        .sidebar-footer .user .avatar {
            width: 36px; height: 36px; border-radius: 50%; background: #0E9F6E;
            display: flex; align-items: center; justify-content: center; color: #fff;
            font-weight: 700; font-size: 0.8rem;
        }
        .sidebar-footer .user .info .name { font-weight: 600; font-size: 0.85rem; color: #1F2937; }
        .sidebar-footer .user .info .role { font-size: 0.7rem; color: #6B7280; }
        
        /* Main Content */
        .main-content { margin-left: 260px; padding: 24px 32px; min-height: 100vh; }
        
        /* Form Styles */
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.375rem;
        }
        .form-control, .form-select {
            width: 100%;
            padding: 0.625rem 1rem;
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            background-color: #FFFFFF;
            color: #1F2937;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #0E9F6E;
            box-shadow: 0 0 0 3px rgba(14,159,110,0.2);
        }
        .form-control::placeholder {
            color: #9CA3AF;
        }
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.5rem;
        }
        
        /* Alert */
        .alert-danger {
            padding: 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F4F5F7; }
        ::-webkit-scrollbar-thumb { background: #0E9F6E; border-radius: 10px; }
        
        /* Preview Image */
        .preview-container {
            min-height: 220px;
            border: 2px dashed #E5E7EB;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-container.has-image {
            border-color: #0E9F6E;
            border-style: solid;
            background-color: #F9FAFB;
        }
        .preview-image {
            max-height: 180px;
            width: auto;
            border-radius: 0.5rem;
            object-fit: cover;
        }
        
        /* Input dengan prefix - FIXED */
        .input-group-prefix {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }
        .input-group-prefix .input-prefix {
            display: flex;
            align-items: center;
            padding: 0 12px;
            background-color: #F3F4F6;
            border: 1px solid #E5E7EB;
            border-right: none;
            border-radius: 0.75rem 0 0 0.75rem;
            font-size: 0.75rem;
            color: #6B7280;
            white-space: nowrap;
            font-weight: 500;
            min-width: fit-content;
        }
        .input-group-prefix .form-control {
            border-radius: 0 0.75rem 0.75rem 0;
            border-left: none;
            flex: 1;
            min-width: 0;
        }
        .input-group-prefix .form-control:focus {
            border-color: #0E9F6E;
            border-left: none;
        }
        .input-group-prefix .form-control:focus + .input-prefix {
            border-color: #0E9F6E;
        }
        
        /* Instagram khusus - tanpa background */
        .input-group-prefix.instagram-group .input-prefix {
            background: transparent;
            border: none;
            padding: 0 0 0 12px;
            border-radius: 0;
            font-size: 0.875rem;
            color: #9CA3AF;
        }
        .input-group-prefix.instagram-group .form-control {
            border-radius: 0.75rem;
            border-left: 1px solid #E5E7EB;
            padding-left: 8px;
        }
        .input-group-prefix.instagram-group .form-control:focus {
            border-left: 1px solid #0E9F6E;
        }
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 16px 8px; }
            .sidebar-brand .text, .sidebar-menu li a span, .sidebar-menu li a .badge, .sidebar-footer .user .info { display: none; }
            .sidebar-brand { justify-content: center; padding-bottom: 16px; }
            .sidebar-menu li a { justify-content: center; padding: 12px; gap: 0; }
            .sidebar-menu li a i { font-size: 1.2rem; }
            .main-content { margin-left: 70px; padding: 16px; }
            .sidebar-footer { padding: 12px; }
            .sidebar-footer .user { justify-content: center; }
            .input-group-prefix .input-prefix {
                font-size: 0.65rem;
                padding: 0 8px;
            }
        }
        
        @media (max-width: 480px) {
            .sidebar { width: 60px; padding: 12px 6px; }
            .main-content { margin-left: 60px; padding: 12px; }
            .input-group-prefix .input-prefix {
                font-size: 0.55rem;
                padding: 0 6px;
            }
        }
    </style>
</head>
<body>

<!-- ============================================================
SIDEBAR
============================================================ -->
<aside class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <img src="../../assets/logo/logo-smp-al-islam.png" alt="Logo" />
        <div class="text">
            <div class="name">SMP Al Islam</div>
            <div class="tag">Administrator</div>
        </div>
    </div>

    <!-- Menu -->
    <ul class="sidebar-menu">
        <li>
            <a href="../dashboard.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="../berita/index.php">
                <i class="bi bi-newspaper"></i>
                <span>Berita</span>
                <span class="badge"><?= $totalBerita ?></span>
            </a>
        </li>
        <li class="active">
            <a href="index.php">
                <i class="bi bi-person"></i>
                <span>Guru</span>
                <span class="badge"><?= $totalGuru ?></span>
            </a>
        </li>
        <li>
            <a href="../prestasi/index.php">
                <i class="bi bi-trophy"></i>
                <span>Prestasi</span>
                <span class="badge"><?= $totalPrestasi ?></span>
            </a>
        </li>
        
        <div class="sidebar-divider"></div>
        
        <li>
            <a href="../logout.php" class="text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>

    <!-- Footer -->
    <div class="sidebar-footer">
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
    <header class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-plus-lg text-smp"></i>
                Tambah Guru
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Tambahkan data guru baru</p>
        </div>
        <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all text-sm font-medium">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </header>

    <!-- ERROR ALERT -->
    <?php if($error): ?>
    <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fade-in alert-danger">
        <i class="bi bi-exclamation-circle text-lg"></i>
        <span class="flex-1"><?= htmlspecialchars($error) ?></span>
        <button type="button" class="text-red-400 hover:text-red-600" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- FORM CARD -->
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-person text-smp text-lg"></i>
                <h3 class="font-semibold text-gray-800">Formulir Data Guru</h3>
            </div>
        </div>
        
        <div class="p-6">
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Kolom Kiri (2/3) -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>
                        
                        <!-- Jabatan & Mata Pelajaran -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Jabatan <span class="text-red-500">*</span></label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Wakil Kepala Sekolah">Wakil Kepala Sekolah</option>
                                    <option value="Guru">Guru</option>
                                    <option value="Staff Administrasi">Staff Administrasi</option>
                                    <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Mata Pelajaran</label>
                                <input type="text" name="mapel" class="form-control" placeholder="Contoh: Matematika">
                            </div>
                        </div>
                        
                        <!-- Tahun Bergabung & Pendidikan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Tahun Bergabung</label>
                                <input type="number" name="tahun_bergabung" class="form-control" placeholder="Masukkan tahun" min="1990" max="<?= date('Y') ?>">
                                <p class="text-xs text-gray-400 mt-1.5">
                                    <i class="bi bi-info-circle"></i> Maksimal tahun <?= date('Y') ?> (tahun berjalan)
                                </p>
                            </div>
                            <div>
                                <label class="form-label">Pendidikan</label>
                                <input type="text" name="pendidikan" class="form-control" placeholder="Contoh: S2 Pendidikan">
                            </div>
                        </div>
                        
                        <!-- Instagram & LinkedIn -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Instagram</label>
                                <div class="input-group-prefix instagram-group">
                                    <span class="input-prefix">@</span>
                                    <input type="text" name="instagram" class="form-control" placeholder="Username Instagram">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">LinkedIn</label>
                                <div class="input-group-prefix">
                                    <span class="input-prefix">linkedin.com/in/</span>
                                    <input type="text" name="linkedin" class="form-control" placeholder="username">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@sekolah.sch.id">
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Kolom Kanan (1/3) - Foto -->
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                            <p class="text-xs text-gray-400 mt-1.5">
                                <i class="bi bi-info-circle"></i> Format: JPG, PNG, WebP. Maks 5MB
                            </p>
                        </div>
                        
                        <!-- Preview Foto -->
                        <div id="previewContainer" class="preview-container">
                            <div id="previewPlaceholder">
                                <i class="bi bi-image text-5xl text-gray-300"></i>
                                <p class="text-sm text-gray-400 mt-2">Preview foto akan muncul di sini</p>
                            </div>
                            <img id="previewImage" class="hidden preview-image" alt="Preview Foto" />
                        </div>
                        
                        <!-- Informasi -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-500 flex items-start gap-2">
                                <i class="bi bi-info-circle text-smp mt-0.5"></i>
                                <span>Foto akan digunakan untuk profil guru di halaman publik sekolah.</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <hr class="my-6 border-gray-200">
                
                <!-- Tombol Aksi -->
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-smp hover:bg-smp-dark text-white px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:shadow-lg text-sm font-medium">
                        <i class="bi bi-save"></i> Simpan Guru
                    </button>
                    <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all text-sm font-medium">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-sm text-gray-400 border-t border-gray-200 mt-8 pt-4">
        <i class="bi bi-shield-check text-smp"></i>
        SMP Al Islam Krian &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

<!-- ===== PREVIEW IMAGE SCRIPT ===== -->
<script>
function previewImage(event) {
    const container = document.getElementById('previewContainer');
    const placeholder = document.getElementById('previewPlaceholder');
    const preview = document.getElementById('previewImage');
    
    if (event.target.files && event.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            container.classList.add('has-image');
        }
        reader.readAsDataURL(event.target.files[0]);
    } else {
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        container.classList.remove('has-image');
    }
}
</script>

</body>
</html>