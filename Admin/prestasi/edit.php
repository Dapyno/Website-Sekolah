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

// ===== AMBIL DATA STATISTIK DARI DATABASE =====
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
    <title>Edit Prestasi - Admin SMP Al Islam Krian</title>
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
    <link href="../css/admin-style.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #F4F5F7; }
        .form-label { @apply block text-sm font-semibold text-gray-700 mb-1.5; }
        .form-control {
            @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-smp/20 focus:border-smp bg-gray-50/50 transition-all;
        }
        .form-control:focus { @apply bg-white; }
        .form-control::placeholder { @apply text-gray-400; }
        .form-select {
            @apply w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-smp/20 focus:border-smp bg-gray-50/50 transition-all appearance-none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 44px;
        }
        .form-select:focus { @apply bg-white; }
        .form-text { @apply text-xs text-gray-400 mt-1.5; }
        .alert-danger { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>
<body>

<div class="flex h-screen overflow-hidden bg-gray-50">

    <!-- ============================================================
    SIDEBAR - Tailwind
    ============================================================ -->
    <aside class="w-64 bg-white/80 backdrop-blur-md border-r border-gray-200/50 flex-shrink-0 overflow-y-auto admin-sidebar">
        
        <div class="flex items-center gap-3 px-5 py-6 border-b border-gray-200/50">
            <img src="../../assets/logo/logo-smp-al-islam.png" alt="Logo" class="h-10 w-10 rounded-xl object-cover" />
            <div>
                <div class="font-bold text-gray-800 text-sm">SMP Al Islam</div>
                <div class="text-xs text-gray-400 uppercase tracking-wider">Administrator</div>
            </div>
        </div>

        <nav class="p-3">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</div>
            
            <a href="../dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all">
                <i class="bi bi-grid text-lg"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            
            <a href="../berita/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all mt-1">
                <i class="bi bi-newspaper text-lg"></i>
                <span class="text-sm font-medium">Berita</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full"><?= $totalBerita ?></span>
            </a>
            
            <a href="../guru/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all mt-1">
                <i class="bi bi-person text-lg"></i>
                <span class="text-sm font-medium">Guru</span>
                <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-0.5 rounded-full"><?= $totalGuru ?></span>
            </a>
            
            <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all mt-1">
                <i class="bi bi-trophy text-lg"></i>
                <span class="text-sm font-medium">Prestasi</span>
                <span class="ml-auto bg-yellow-100 text-yellow-600 text-xs px-2 py-0.5 rounded-full"><?= $totalPrestasi ?></span>
            </a>
            
            <div class="border-t border-gray-200/50 my-3"></div>
            
            <a href="../logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 transition-all mt-1">
                <i class="bi bi-box-arrow-right text-lg"></i>
                <span class="text-sm font-medium">Logout</span>
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200/50 bg-white/80 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-smp text-white flex items-center justify-center font-bold text-sm">
                    <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800"><?= $_SESSION['nama'] ?></div>
                    <div class="text-xs text-gray-400">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============================================================
    MAIN CONTENT - Tailwind
    ============================================================ -->
    <main class="flex-1 overflow-y-auto p-6">

        <!-- HEADER -->
        <header class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200/50">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="bi bi-pencil text-smp mr-2"></i>Edit Prestasi
                </h1>
                <p class="text-sm text-gray-400 mt-0.5">Edit data prestasi sekolah</p>
            </div>
            <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all text-sm font-medium">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </header>

        <!-- ALERT ERROR -->
        <?php if($error): ?>
        <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fade-in alert-danger">
            <i class="bi bi-exclamation-circle text-lg"></i>
            <span class="flex-1"><?= $error ?></span>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- FORM -->
        <div class="mt-6 bg-white/90 backdrop-blur-sm rounded-2xl border border-gray-100/50 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="bi bi-pencil-square text-smp"></i>
                    Form Edit Prestasi
                </h3>
            </div>
            
            <div class="p-6">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        <!-- Kolom Kiri (2/3) -->
                        <div class="lg:col-span-2 space-y-4">
                            <!-- Nama Prestasi -->
                            <div>
                                <label class="form-label">
                                    Nama Prestasi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_prestasi" class="form-control" 
                                    value="<?= htmlspecialchars($prestasi['nama_prestasi']) ?>" required />
                            </div>

                            <!-- Tingkat & Tahun -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">
                                        Tingkat <span class="text-red-500">*</span>
                                    </label>
                                    <select name="tingkat" class="form-select" required>
                                        <option value="internasional" <?= $prestasi['tingkat'] == 'internasional' ? 'selected' : '' ?>>🌍 Internasional</option>
                                        <option value="nasional" <?= $prestasi['tingkat'] == 'nasional' ? 'selected' : '' ?>>🇮🇩 Nasional</option>
                                        <option value="provinsi" <?= $prestasi['tingkat'] == 'provinsi' ? 'selected' : '' ?>>🏅 Provinsi</option>
                                        <option value="kabupaten" <?= $prestasi['tingkat'] == 'kabupaten' ? 'selected' : '' ?>>🏆 Kabupaten</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">
                                        Tahun <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="tahun" class="form-control" 
                                        value="<?= $prestasi['tahun'] ?>" min="2000" max="<?= date('Y') + 1 ?>" required />
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($prestasi['deskripsi']) ?></textarea>
                            </div>

                            <!-- Siswa & Lokasi -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Siswa / Tim</label>
                                    <input type="text" name="siswa" class="form-control" 
                                        value="<?= htmlspecialchars($prestasi['siswa']) ?>" />
                                </div>
                                <div>
                                    <label class="form-label">Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" 
                                        value="<?= htmlspecialchars($prestasi['lokasi']) ?>" />
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan (1/3) - Gambar -->
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Gambar</label>
                                <input type="file" name="gambar" class="form-control p-2" accept="image/*" />
                                <p class="form-text">Kosongkan jika tidak ingin mengganti</p>
                            </div>
                            
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center bg-gray-50/50 min-h-[180px] flex items-center justify-center">
                                <?php if($prestasi['gambar'] && file_exists('../../assets/prestasi/' . $prestasi['gambar'])): ?>
                                <img src="../../assets/prestasi/<?= $prestasi['gambar'] ?>" class="max-h-[160px] rounded-lg object-contain" />
                                <?php else: ?>
                                <div>
                                    <i class="bi bi-image text-4xl text-gray-300 block mb-2"></i>
                                    <p class="text-sm text-gray-400">Tidak ada gambar</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" class="bg-smp hover:bg-smp-dark text-white px-6 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:shadow-lg text-sm font-medium">
                            <i class="bi bi-save"></i> Update Prestasi
                        </button>
                        <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl transition-all text-sm font-medium">
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
</div>

</body>
</html>