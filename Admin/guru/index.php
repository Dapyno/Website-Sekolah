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
$guruList = $guruModel->getAll();

// AMBIL DATA STATISTIK DARI DATABASE
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
    <title>Kelola Guru - Admin SMP Al Islam Krian</title>
    <link rel="icon" type="image/png" sizes="64x64" href="../../assets/logo/logo-smp-al-islam.png" />
    
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
        
        /* Alert */
        .alert-success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; }
        .alert-danger { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease forwards; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F4F5F7; }
        ::-webkit-scrollbar-thumb { background: #0E9F6E; border-radius: 10px; }
        
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

<!-- SIDEBAR -->
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

<!-- MAIN CONTENT -->
<main class="main-content">

    <!-- HEADER -->
    <header class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="bi bi-person text-smp"></i>
                Kelola Guru
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola data guru dan tenaga pendidik</p>
        </div>
        <a href="create.php" class="bg-smp hover:bg-smp-dark text-white px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:shadow-lg text-sm font-medium">
            <i class="bi bi-plus-lg"></i> Tambah Guru
        </a>
    </header>

    <!-- ALERT -->
    <?php if($message): ?>
    <div class="mt-4 p-4 rounded-xl flex items-center gap-3 animate-fade-in <?= $messageType == 'success' ? 'alert-success' : 'alert-danger' ?>">
        <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-circle' ?> text-lg"></i>
        <span class="flex-1"><?= $message ?></span>
        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <i class="bi bi-person text-smp text-lg"></i>
                <h3 class="font-semibold text-gray-800">Semua Guru</h3>
                <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full"><?= $totalGuru ?></span>
            </div>
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Cari guru..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-smp/20 focus:border-smp bg-gray-50 w-48" />
            </div>
        </div>

        <!-- Table Body -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($guruList)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i class="bi bi-person-x text-4xl block mb-3 text-gray-300"></i>
                            <p class="font-medium">Belum ada data guru</p>
                            <p class="text-sm mt-1">Klik tombol <span class="text-smp font-medium">"Tambah Guru"</span></p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach($guruList as $g): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <?php if($g['foto'] && file_exists('../../assets/guru/' . $g['foto'])): ?>
                            <img src="../../assets/guru/<?= $g['foto'] ?>" class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" />
                            <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                <i class="bi bi-person text-gray-300 text-xl"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($g['nama']) ?></td>
                        <td class="px-4 py-3">
                            <?= getJabatanBadge($g['jabatan']) ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($g['mapel'] ?? '-') ?></td>
                        <td class="px-4 py-3">
                            <?= getStatusBadge($g['status']) ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                <a href="edit.php?id=<?= $g['id'] ?>" class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 transition-colors" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete.php?id=<?= $g['id'] ?>" class="p-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Hapus" onclick="return confirm('Yakin ingin menghapus guru ini?')">
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

        <!-- Pagination -->
        <?php if(!empty($guruList) && count($guruList) > 10): ?>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center flex-wrap gap-3">
            <p class="text-sm text-gray-400">Menampilkan 1-<?= min(10, count($guruList)) ?> dari <?= count($guruList) ?> data</p>
            <div class="flex gap-1">
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition-colors text-sm">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="px-3.5 py-1.5 rounded-lg bg-smp text-white text-sm font-medium">1</button>
                <button class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors text-sm">2</button>
                <button class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors text-sm">3</button>
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition-colors text-sm">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="text-center text-sm text-gray-400 border-t border-gray-200 mt-8 pt-4">
        <i class="bi bi-shield-check text-smp"></i>
        SMP Al Islam Krian &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

</body>
</html>