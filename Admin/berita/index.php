<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

require_once '../../Backend/config/database.php';
require_once '../../Backend/models/Berita.php';

$beritaModel = new Berita($pdo);
$beritaList = $beritaModel->getAll();

$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$totalBerita = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$totalGuru = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$totalPrestasi = $stmt->fetch()['total'] ?? 0;

$message = $_GET['msg'] ?? '';
$messageType = $_GET['type'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Admin SMP Islam Watestanjung</title>
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
    <link rel="stylesheet" href="../css/berita/berita.css">
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
                <i class="bi bi-newspaper text-smp"></i> Kelola Berita
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola berita dan informasi sekolah</p>
        </div>
        <a href="create.php" class="bg-smp hover:bg-smp-dark text-white px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:shadow-lg text-sm font-medium">
            <i class="bi bi-plus-lg"></i> Tambah Berita
        </a>
    </header>

    <?php if($message): ?>
    <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fade-in <?= $messageType == 'success' ? 'alert-success' : 'alert-danger' ?>">
        <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-circle' ?> text-lg"></i>
        <span class="flex-1"><?= $message ?></span>
        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php endif; ?>

    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <i class="bi bi-newspaper text-smp text-lg"></i>
                <h3 class="font-semibold text-gray-800">Semua Berita</h3>
                <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full"><?= $totalBerita ?></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Gambar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($beritaList)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <i class="bi bi-newspaper text-4xl block mb-3 text-gray-300"></i>
                            <p class="font-medium">Belum ada data berita</p>
                            <p class="text-sm mt-1">Klik tombol <span class="text-smp font-medium">"Tambah Berita"</span></p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach($beritaList as $b): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <?php if($b['gambar'] && file_exists('../../assets/berita/' . $b['gambar'])): ?>
                            <img src="../../assets/berita/<?= $b['gambar'] ?>" class="w-14 h-10 rounded-lg object-cover border border-gray-100" />
                            <?php else: ?>
                            <div class="w-14 h-10 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                <i class="bi bi-image text-gray-300 text-lg"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 max-w-xs truncate"><?= htmlspecialchars($b['judul']) ?></td>
                        <td class="px-4 py-3">
                            <?php 
                            $kategoriClasses = [
                                'prestasi' => 'bg-yellow-100 text-yellow-700',
                                'acara' => 'bg-purple-100 text-purple-700',
                                'pendidikan' => 'bg-blue-100 text-blue-700',
                                'pengumuman' => 'bg-green-100 text-green-700'
                            ];
                            $kategoriLabels = [
                                'prestasi' => '🏆 Prestasi',
                                'acara' => '🎉 Acara',
                                'pendidikan' => '📚 Pendidikan',
                                'pengumuman' => '📢 Pengumuman'
                            ];
                            $kat = $b['kategori'] ?? 'pendidikan';
                            $class = $kategoriClasses[$kat] ?? 'bg-gray-100 text-gray-700';
                            $label = $kategoriLabels[$kat] ?? ucfirst($kat);
                            ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $class ?>">
                                <?= $label ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= date('d-m-Y', strtotime($b['tanggal'])) ?></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                <a href="edit.php?id=<?= $b['id'] ?>" class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete.php?id=<?= $b['id'] ?>" class="p-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Hapus" onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center text-sm text-gray-400 border-t border-gray-200 mt-8 pt-4">
        <i class="bi bi-shield-check text-smp"></i>
        SMP Islam Watestanjung &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

</body>
</html>