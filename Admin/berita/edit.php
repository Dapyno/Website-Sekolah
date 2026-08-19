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
    <title>Edit Berita - Admin SMP Islam Watestanjung</title>
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
    <link rel="stylesheet" href="../css/berita/edit.css">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="../../assets/logo/logo-smp-islam.png" alt="Logo SMP Islam Watestanjung" />
        <div class="text">
            <div class="name">SMP Islam</div>
            <div class="tag">Watestanjung</div>
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
                        
                        <!-- Preview Box -->
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
        SMP Islam Watestanjung &bull; Dashboard Administrator &bull; <?= date('Y') ?>
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