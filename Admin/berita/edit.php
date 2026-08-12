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
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0) {
    header('Location: index.php?msg=ID berita tidak valid&type=danger');
    exit();
}

$berita = $beritaModel->getById($id);

if(!$berita) {
    header('Location: index.php?msg=Berita tidak ditemukan&type=danger');
    exit();
}

$error = '';

$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$totalBerita = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$totalGuru = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$totalPrestasi = $stmt->fetch()['total'] ?? 0;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'] ?? '';
    $kategori = $_POST['kategori'] ?? 'pendidikan';
    $konten = $_POST['konten'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $gambar = $berita['gambar'];
    
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadResult = uploadGambar($_FILES['gambar'], '../../assets/berita/');
        if($uploadResult['success']) {
            if($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar']) && $berita['gambar'] != 'default.jpg') {
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
    <title>Edit Berita - Admin SMP Al Islam Krian</title>
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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #F4F5F7; }
        
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
        
        .main-content { margin-left: 260px; padding: 24px 32px; min-height: 100vh; }
        
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
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
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
        
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F4F5F7; }
        ::-webkit-scrollbar-thumb { background: #0E9F6E; border-radius: 10px; }
        
        /* Preview Box - Style seperti yang diminta */
        .preview-box {
            min-height: 200px;
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            background-color: #F9FAFB;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .preview-box img {
            max-height: 180px;
            width: auto;
            border-radius: 0.5rem;
            object-fit: contain;
        }
        .preview-box .preview-placeholder i {
            font-size: 3rem;
            color: #9CA3AF;
        }
        .preview-box .preview-placeholder p {
            color: #9CA3AF;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .preview-box.has-image {
            border-color: #0E9F6E;
            background-color: #FFFFFF;
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
        }
        
        @media (max-width: 480px) {
            .sidebar { width: 60px; padding: 12px 6px; }
            .main-content { margin-left: 60px; padding: 12px; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="../../assets/logo/logo-smp-al-islam.png" alt="Logo" />
        <div class="text">
            <div class="name">SMP Al Islam</div>
            <div class="tag">Administrator</div>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li><a href="../dashboard.php"><i class="bi bi-grid"></i><span>Dashboard</span></a></li>
        <li class="active"><a href="index.php"><i class="bi bi-newspaper"></i><span>Berita</span><span class="badge"><?= $totalBerita ?></span></a></li>
        <li><a href="../guru/index.php"><i class="bi bi-person"></i><span>Guru</span><span class="badge"><?= $totalGuru ?></span></a></li>
        <li><a href="../prestasi/index.php"><i class="bi bi-trophy"></i><span>Prestasi</span><span class="badge"><?= $totalPrestasi ?></span></a></li>
        <div class="sidebar-divider"></div>
        <li><a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
    </ul>

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

<main class="main-content">

    <header class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-pencil text-smp"></i> Edit Berita
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Edit data berita</p>
        </div>
        <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all text-sm font-medium">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </header>

    <?php if($error): ?>
    <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fade-in alert-danger">
        <i class="bi bi-exclamation-circle text-lg"></i>
        <span class="flex-1"><?= htmlspecialchars($error) ?></span>
        <button type="button" class="text-red-400 hover:text-red-600" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php endif; ?>

    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-newspaper text-smp text-lg"></i>
                <h3 class="font-semibold text-gray-800">Formulir Edit Berita</h3>
            </div>
        </div>
        
        <div class="p-6">
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Kolom Kiri (2/3) -->
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <label class="form-label">Judul Berita <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($berita['judul']) ?>" required>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                                <select name="kategori" class="form-select" required>
                                    <option value="prestasi" <?= $berita['kategori']=='prestasi'?'selected':'' ?>>🏆 Prestasi</option>
                                    <option value="acara" <?= $berita['kategori']=='acara'?'selected':'' ?>>🎉 Acara</option>
                                    <option value="pendidikan" <?= $berita['kategori']=='pendidikan'?'selected':'' ?>>📚 Pendidikan</option>
                                    <option value="pengumuman" <?= $berita['kategori']=='pengumuman'?'selected':'' ?>>📢 Pengumuman</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="<?= $berita['tanggal'] ?>" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label">Konten <span class="text-red-500">*</span></label>
                            <textarea name="konten" class="form-control" rows="8" required><?= htmlspecialchars($berita['konten']) ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Kolom Kanan (1/3) - Gambar -->
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Gambar</label>
                            <input type="file" name="gambar" id="gambarInput" class="form-control" accept="image/*" onchange="previewImage(event)">
                            <p class="text-xs text-gray-400 mt-1.5">
                                <i class="bi bi-info-circle"></i> Kosongkan jika tidak ingin mengganti
                            </p>
                        </div>
                        
                        <!-- Preview Box - Seperti yang diminta -->
                        <div id="previewBox" class="preview-box <?= ($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) ? 'has-image' : '' ?>">
                            <div id="previewPlaceholder" style="<?= ($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) ? 'display:none;' : 'display:flex;' ?>">
                                <i class="bi bi-image"></i>
                                <p>Tidak ada gambar</p>
                            </div>
                            <img id="previewImage" 
                                style="<?= ($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) ? 'display:block; max-height:180px; border-radius:0.5rem; object-fit:contain;' : 'display:none;' ?>" 
                                src="<?= ($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) ? '../../assets/berita/' . $berita['gambar'] : '' ?>" 
                                alt="Preview Gambar" />
                        </div>
                    </div>
                </div>
                
                <hr class="my-6 border-gray-200">
                
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-smp hover:bg-smp-dark text-white px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:shadow-lg text-sm font-medium">
                        <i class="bi bi-save"></i> Update Berita
                    </button>
                    <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all text-sm font-medium">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center text-sm text-gray-400 border-t border-gray-200 mt-8 pt-4">
        <i class="bi bi-shield-check text-smp"></i>
        SMP Al Islam Krian &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

<script>
var oldImagePath = '<?= ($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) ? '../../assets/berita/' . $berita['gambar'] : '' ?>';
var hasOldImage = <?= ($berita['gambar'] && file_exists('../../assets/berita/' . $berita['gambar'])) ? 'true' : 'false' ?>;

function previewImage(event) {
    var previewBox = document.getElementById('previewBox');
    var placeholder = document.getElementById('previewPlaceholder');
    var preview = document.getElementById('previewImage');
    var file = event.target.files[0];
    
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            previewBox.classList.add('has-image');
        }
        reader.readAsDataURL(file);
    } else {
        // Kembalikan ke gambar lama jika ada
        if (hasOldImage && oldImagePath) {
            preview.src = oldImagePath;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            previewBox.classList.add('has-image');
        } else {
            preview.src = '';
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            previewBox.classList.remove('has-image');
        }
    }
}
</script>

</body>
</html>