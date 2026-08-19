<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../Backend/config/database.php';
require_once '../Backend/models/Berita.php';
require_once '../Backend/helpers/functions.php';

// ===== STATISTIK =====
$beritaModel = new Berita($pdo);
$beritaList = $beritaModel->getAll();
$beritaCount = count($beritaList);

// 2. Guru
$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$guruCount = $stmt->fetch()['total'] ?? 0;

// 3. Prestasi
$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$prestasiCount = $stmt->fetch()['total'] ?? 0;

// 4. Agenda
$stmt = $pdo->query("SELECT COUNT(*) as total FROM agenda");
$agendaCount = $stmt->fetch()['total'] ?? 0;

// 5. Aktivitas terbaru
$aktivitasList = [];

// Berita terbaru
$beritaTerbaru = array_slice($beritaList, 0, 3);
foreach($beritaTerbaru as $b) {
    $aktivitasList[] = [
        'icon' => 'bi-newspaper',
        'icon_color' => 'green',
        'text' => 'Berita "' . substr($b['judul'], 0, 30) . '..." ditambahkan',
        'time' => formatTanggal($b['tanggal'])
    ];
}

// Prestasi terbaru
$stmt = $pdo->query("SELECT * FROM prestasi ORDER BY id DESC LIMIT 2");
$prestasiTerbaru = $stmt->fetchAll();
foreach($prestasiTerbaru as $p) {
    $aktivitasList[] = [
        'icon' => 'bi-trophy',
        'icon_color' => 'gold',
        'text' => 'Prestasi "' . substr($p['nama_prestasi'], 0, 30) . '..." ditambahkan',
        'time' => $p['tahun'] ?? 'Tahun ' . date('Y')
    ];
}

// Agenda terbaru
$stmt = $pdo->query("SELECT * FROM agenda ORDER BY id DESC LIMIT 2");
$agendaTerbaru = $stmt->fetchAll();
foreach($agendaTerbaru as $a) {
    $aktivitasList[] = [
        'icon' => 'bi-calendar-check',
        'icon_color' => 'blue',
        'text' => 'Agenda "' . substr($a['judul'], 0, 30) . '..." ditambahkan',
        'time' => formatTanggal($a['tanggal'] ?? date('Y-m-d'))
    ];
}

usort($aktivitasList, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});
$aktivitasList = array_slice($aktivitasList, 0, 5);

// ===== PERSENTASE =====
$lastMonthBerita = max(0, $beritaCount - 3);
$lastMonthPrestasi = max(0, $prestasiCount - 1);
$lastMonthGuru = max(0, $guruCount - 1);
$lastMonthAgenda = max(0, $agendaCount - 1);

$changeBerita = $beritaCount > 0 ? round(($beritaCount - $lastMonthBerita) / max(1, $lastMonthBerita) * 100) : 0;
$changePrestasi = $prestasiCount > 0 ? round(($prestasiCount - $lastMonthPrestasi) / max(1, $lastMonthPrestasi) * 100) : 0;
$changeGuru = $guruCount > 0 ? round(($guruCount - $lastMonthGuru) / max(1, $lastMonthGuru) * 100) : 0;
$changeAgenda = $agendaCount > 0 ? round(($agendaCount - $lastMonthAgenda) / max(1, $lastMonthAgenda) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin SMP Islam Watestanjung</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/logo/logo-smp-islam.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/logo/logo-smp-islam.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/logo/logo-smp-islam.png" />
    
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
    <link href="css/admin-style.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #F4F5F7; }
        .stat-change.positive { color: #22C55E; }
        .stat-change.negative { color: #EF4444; }
        
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
<aside class="sidebar" style="width:260px; background:rgba(255,255,255,0.85); backdrop-filter:blur(20px); border-right:1px solid rgba(229,231,235,0.5); height:100vh; position:fixed; top:0; left:0; overflow-y:auto; z-index:50; padding:24px 16px;">

    <!-- Brand -->
    <div class="flex items-center gap-3 pb-6 border-b border-gray-200/50 mb-5">
        <img src="../assets/logo/logo-smp-islam.png" alt="Logo SMP Islam Watestanjung" class="h-10 w-10 rounded-xl object-cover" />
        <div>
            <div class="font-bold text-gray-800 text-sm">SMP Islam</div>
            <div class="text-xs text-gray-400 uppercase tracking-wider">Watestanjung</div>
        </div>
    </div>

    <!-- Menu -->
    <ul class="list-none p-0 m-0">
        <li class="mb-1">
            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-smp text-white hover:bg-smp-dark transition-all">
                <i class="bi bi-grid text-lg"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
        </li>
        <li class="mb-1">
            <a href="berita/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all">
                <i class="bi bi-newspaper text-lg"></i>
                <span class="text-sm font-medium">Berita</span>
                <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-0.5 rounded-full"><?= $beritaCount ?></span>
            </a>
        </li>
        <li class="mb-1">
            <a href="guru/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all">
                <i class="bi bi-person text-lg"></i>
                <span class="text-sm font-medium">Guru</span>
                <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-0.5 rounded-full"><?= $guruCount ?></span>
            </a>
        </li>
        <li class="mb-1">
            <a href="prestasi/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-600 hover:bg-gray-100 transition-all">
                <i class="bi bi-trophy text-lg"></i>
                <span class="text-sm font-medium">Prestasi</span>
                <span class="ml-auto bg-yellow-100 text-yellow-600 text-xs px-2 py-0.5 rounded-full"><?= $prestasiCount ?></span>
            </a>
        </li>
        
        <div class="border-t border-gray-200/50 my-3"></div>
        
        <li class="mb-1">
            <a href="logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 transition-all">
                <i class="bi bi-box-arrow-right text-lg"></i>
                <span class="text-sm font-medium">Logout</span>
            </a>
        </li>
    </ul>

    <!-- User Mini -->
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

<!-- MAIN CONTENT -->
<main class="main-content" style="margin-left:260px; padding:24px 32px; min-height:100vh;">

    <!-- HEADER -->
    <header class="flex justify-between items-center flex-wrap gap-3 pb-6 border-b border-gray-200/50">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-400">Selamat datang kembali, <?= $_SESSION['nama'] ?> 👋</p>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative hidden md:block">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-smp/20 focus:border-smp w-48 lg:w-56 bg-gray-50/50" />
            </div>
            
            <!-- Notification -->
            <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="bi bi-bell text-xl"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            
            <!-- Profile -->
            <div class="flex items-center gap-3 px-3 py-1.5 rounded-full bg-gray-50/50 hover:bg-gray-100 transition-colors cursor-pointer">
                <div class="w-8 h-8 rounded-full bg-smp text-white flex items-center justify-center font-bold text-sm">
                    <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                </div>
                <div class="hidden sm:block">
                    <div class="text-sm font-semibold text-gray-800"><?= $_SESSION['nama'] ?></div>
                    <div class="text-xs text-gray-400">Admin</div>
                </div>
                <i class="bi bi-chevron-down text-gray-400 text-sm"></i>
            </div>
        </div>
    </header>

    <!--
    STATISTICS CARDS
 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        
        <!-- Card 1: Berita -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Total Berita</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1"><?= $beritaCount ?></p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <i class="bi bi-newspaper text-blue-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm">
                <span class="stat-change <?= $changeBerita >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $changeBerita >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($changeBerita) ?>%
                </span>
                <span class="text-gray-400">dari bulan lalu</span>
            </div>
        </div>
        
        <!-- Card 2: Prestasi -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Total Prestasi</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1"><?= $prestasiCount ?></p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-xl">
                    <i class="bi bi-trophy text-yellow-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm">
                <span class="stat-change <?= $changePrestasi >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $changePrestasi >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($changePrestasi) ?>%
                </span>
                <span class="text-gray-400">dari bulan lalu</span>
            </div>
        </div>
        
        <!-- Card 3: Guru -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Total Guru</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1"><?= $guruCount ?></p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl">
                    <i class="bi bi-person text-green-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm">
                <span class="stat-change <?= $changeGuru >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $changeGuru >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($changeGuru) ?>%
                </span>
                <span class="text-gray-400">dari bulan lalu</span>
            </div>
        </div>
        
        <!-- Card 4: Agenda -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-400 font-medium">Total Agenda</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1"><?= $agendaCount ?></p>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl">
                    <i class="bi bi-calendar text-purple-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm">
                <span class="stat-change <?= $changeAgenda >= 0 ? 'positive' : 'negative' ?>">
                    <i class="bi bi-arrow-<?= $changeAgenda >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($changeAgenda) ?>%
                </span>
                <span class="text-gray-400">dari bulan lalu</span>
            </div>
        </div>
    </div>

    <!--
    CONTENT GRID
 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        
        <!-- Berita Terbaru Table -->
        <div class="lg:col-span-2 bg-white/90 backdrop-blur-sm rounded-2xl border border-gray-100/50 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="bi bi-clock-history text-smp text-lg"></i>
                    <h3 class="font-semibold text-gray-800">Berita Terbaru</h3>
                </div>
                <a href="berita/index.php" class="text-sm text-smp hover:underline flex items-center gap-1">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($beritaList)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                Belum ada berita
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php $no=1; foreach(array_slice($beritaList,0,5) as $b): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 text-gray-400"><?= $no++ ?></td>
                            <td class="px-4 py-3 font-medium text-gray-800"><?= substr($b['judul'], 0, 35) ?>...</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full 
                                    <?php if($b['kategori'] == 'prestasi'): ?>bg-blue-100 text-blue-600
                                    <?php elseif($b['kategori'] == 'acara'): ?>bg-yellow-100 text-yellow-600
                                    <?php elseif($b['kategori'] == 'pendidikan'): ?>bg-green-100 text-green-600
                                    <?php else: ?>bg-red-100 text-red-600<?php endif; ?>">
                                    <?= getKategoriLabel($b['kategori']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400"><?= formatTanggal($b['tanggal']) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="berita/edit.php?id=<?= $b['id'] ?>" class="text-blue-500 hover:text-blue-700 transition-colors">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="berita/delete.php?id=<?= $b['id'] ?>" class="text-red-500 hover:text-red-700 transition-colors" onclick="return confirm('Yakin hapus?')">
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

        <!-- Aktivitas Terbaru -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl border border-gray-100/50 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="bi bi-activity text-smp text-lg"></i>
                    <h3 class="font-semibold text-gray-800">Aktivitas Terbaru</h3>
                </div>
            </div>
            <div class="p-4 space-y-3 max-h-[400px] overflow-y-auto">
                <?php if(empty($aktivitasList)): ?>
                <div class="text-center py-8 text-gray-400">
                    <i class="bi bi-inbox text-3xl block mb-2"></i>
                    Belum ada aktivitas
                </div>
                <?php else: ?>
                <?php foreach($aktivitasList as $akt): ?>
                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50/50 transition-colors">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        <?php if($akt['icon_color'] == 'green'): ?>bg-green-100 text-green-600
                        <?php elseif($akt['icon_color'] == 'gold'): ?>bg-yellow-100 text-yellow-600
                        <?php else: ?>bg-blue-100 text-blue-600<?php endif; ?>">
                        <i class="bi <?= $akt['icon'] ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-700"><?= $akt['text'] ?></div>
                        <div class="text-xs text-gray-400 mt-0.5"><?= $akt['time'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-sm text-gray-400 border-t border-gray-200/50 mt-8 pt-4">
        <i class="bi bi-shield-check text-smp"></i>
        SMP Islam Watestanjung &bull; Dashboard Administrator &bull; <?= date('Y') ?>
    </div>
</main>

</body>
</html>