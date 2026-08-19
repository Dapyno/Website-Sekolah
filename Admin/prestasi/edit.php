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
$id = $_GET['id'] ?? 0;
$prestasi = $prestasiModel->getById($id);

if(!$prestasi) {
    header('Location: index.php?msg=Prestasi tidak ditemukan&type=danger');
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_prestasi = $_POST['nama_prestasi'] ?? '';
    $tingkat = $_POST['tingkat'] ?? '';
    $tahun = $_POST['tahun'] ?? date('Y');
    $deskripsi = $_POST['deskripsi'] ?? '';
    $siswa = $_POST['siswa'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $gambar = $prestasi['gambar'];
    
    // Upload gambar baru jika ada
    if(isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadResult = uploadGambar($_FILES['gambar'], '../../assets/prestasi/');
        if($uploadResult['success']) {
            // Hapus gambar lama jika ada dan bukan default
            if($prestasi['gambar'] && file_exists('../../assets/prestasi/' . $prestasi['gambar']) && $prestasi['gambar'] != 'default.jpg') {
                unlink('../../assets/prestasi/' . $prestasi['gambar']);
            }
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
        
        if($prestasiModel->update($id, $data)) {
            header('Location: index.php?msg=Prestasi berhasil diperbarui&type=success');
            exit();
        } else {
            $error = 'Gagal memperbarui prestasi!';
        }
    }
}

// AMBIL DATA STATISTIK DARI DATABASE
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$totalBerita = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$totalGuru = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$totalPrestasi = $stmt->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Prestasi - Admin SMP Islam Watestanjung</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/logo/logo-smp-islam.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/logo/logo-smp-islam.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/logo/logo-smp-islam.png" />
    
    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- TAILWIND CONFIG -->
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
    <link rel="stylesheet" href="../css/prestasi/edit.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <img src="../../assets/logo/logo-smp-islam.png" alt="Logo SMP Islam Watestanjung" />
        <div class="text">
            <div class="name">SMP Islam</div>
            <div class="tag">Watestanjung</div>
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
        <li>
            <a href="../guru/index.php">
                <i class="bi bi-person"></i>
                <span>Guru</span>
                <span class="badge"><?= $totalGuru ?></span>
            </a>
        </li>
        <li class="active">
            <a href="index.php">
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

<!--
MAIN CONTENT -->
<main class="main-content">

    <!-- HEADER -->
    <header class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-pencil text-smp"></i>
                Edit Prestasi
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Edit data prestasi sekolah</p>
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
                <i class="bi bi-pencil-square text-smp text-lg"></i>
                <h3 class="font-semibold text-gray-800">Form Edit Prestasi</h3>
            </div>
        </div>
        
        <div class="p-6">
            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Kolom Kiri (2/3) -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Nama Prestasi -->
                        <div>
                            <label class="form-label">Nama Prestasi <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_prestasi" class="form-control" 
                                value="<?= htmlspecialchars($prestasi['nama_prestasi']) ?>" 
                                placeholder="Masukkan nama prestasi" required>
                        </div>

                        <!-- Tingkat & Tahun -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Tingkat <span class="text-red-500">*</span></label>
                                <select name="tingkat" class="form-select" required>
                                    <option value="internasional" <?= $prestasi['tingkat'] == 'internasional' ? 'selected' : '' ?>>🌍 Internasional</option>
                                    <option value="nasional" <?= $prestasi['tingkat'] == 'nasional' ? 'selected' : '' ?>>🇮🇩 Nasional</option>
                                    <option value="provinsi" <?= $prestasi['tingkat'] == 'provinsi' ? 'selected' : '' ?>>🏅 Provinsi</option>
                                    <option value="kabupaten" <?= $prestasi['tingkat'] == 'kabupaten' ? 'selected' : '' ?>>🏆 Kabupaten</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tahun <span class="text-red-500">*</span></label>
                                <input type="number" name="tahun" class="form-control" 
                                    value="<?= $prestasi['tahun'] ?>" 
                                    min="2000" max="<?= date('Y') + 1 ?>" 
                                    placeholder="Masukkan tahun" required>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4" 
                                placeholder="Tuliskan deskripsi prestasi"><?= htmlspecialchars($prestasi['deskripsi']) ?></textarea>
                        </div>

                        <!-- Siswa & Lokasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Siswa / Tim</label>
                                <input type="text" name="siswa" class="form-control" 
                                    value="<?= htmlspecialchars($prestasi['siswa']) ?>" 
                                    placeholder="Nama siswa atau tim">
                            </div>
                            <div>
                                <label class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control" 
                                    value="<?= htmlspecialchars($prestasi['lokasi']) ?>" 
                                    placeholder="Tempat/lokasi prestasi">
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan (1/3) - Gambar -->
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Gambar</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewImage(event)">
                            <p class="text-xs text-gray-400 mt-1.5">
                                <i class="bi bi-info-circle"></i> Kosongkan jika tidak ingin mengganti
                            </p>
                        </div>
                        
                        <div id="previewContainer" class="preview-container <?= ($prestasi['gambar'] && file_exists('../../assets/prestasi/' . $prestasi['gambar'])) ? 'has-image' : '' ?>">
                            <?php if($prestasi['gambar'] && file_exists('../../assets/prestasi/' . $prestasi['gambar'])): ?>
                            <img id="previewImage" src="../../assets/prestasi/<?= $prestasi['gambar'] ?>" class="preview-image" alt="Preview" />
                            <div id="previewPlaceholder" class="hidden">
                                <i class="bi bi-image text-4xl text-gray-300 block mb-2"></i>
                                <p class="text-sm text-gray-400">Preview foto akan muncul di sini</p>
                            </div>
                            <?php else: ?>
                            <div id="previewPlaceholder">
                                <i class="bi bi-image text-4xl text-gray-300 block mb-2"></i>
                                <p class="text-sm text-gray-400">Tidak ada gambar</p>
                            </div>
                            <img id="previewImage" class="hidden preview-image" alt="Preview" />
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informasi -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-500 flex items-start gap-2">
                                <i class="bi bi-info-circle text-smp mt-0.5"></i>
                                <span>Format: JPG, PNG, WebP. Maks 5MB</span>
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Tombol Aksi -->
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-smp hover:bg-smp-dark text-white px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:shadow-lg text-sm font-medium">
                        <i class="bi bi-save"></i> Update Prestasi
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
        SMP Islam Watestanjung &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

<!-- PREVIEW IMAGE SCRIPT -->
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