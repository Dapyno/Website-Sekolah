<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../Backend/config/database.php';
require_once '../Backend/models/Berita.php';
require_once '../Backend/helpers/functions.php';

// ===== MODEL UNTUK STATISTIK =====
// 1. Berita
$beritaModel = new Berita($pdo);
$beritaList = $beritaModel->getAll();
$beritaCount = count($beritaList);

// 2. Guru - Query langsung
$stmt = $pdo->query("SELECT COUNT(*) as total FROM guru");
$guruCount = $stmt->fetch()['total'] ?? 0;

// 3. Prestasi - Query langsung
$stmt = $pdo->query("SELECT COUNT(*) as total FROM prestasi");
$prestasiCount = $stmt->fetch()['total'] ?? 0;

// 4. Agenda - Query langsung
$stmt = $pdo->query("SELECT COUNT(*) as total FROM agenda");
$agendaCount = $stmt->fetch()['total'] ?? 0;

// 5. Pengumuman - Query langsung
$stmt = $pdo->query("SELECT COUNT(*) as total FROM pengumuman");
$pengumumanCount = $stmt->fetch()['total'] ?? 0;

// 6. User - Query langsung
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$userCount = $stmt->fetch()['total'] ?? 0;

// 7. Aktivitas terbaru - gabungan dari beberapa tabel
$aktivitasList = [];

// Ambil 3 berita terbaru
$beritaTerbaru = array_slice($beritaList, 0, 3);
foreach($beritaTerbaru as $b) {
    $aktivitasList[] = [
        'icon' => 'bi-newspaper',
        'icon_color' => 'green',
        'text' => 'Berita "' . substr($b['judul'], 0, 30) . '..." ditambahkan',
        'time' => formatTanggal($b['tanggal'])
    ];
}

// Ambil 2 prestasi terbaru
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

// Ambil 2 agenda terbaru
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

// Urutkan aktivitas berdasarkan waktu (descending)
usort($aktivitasList, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Ambil 5 aktivitas terbaru
$aktivitasList = array_slice($aktivitasList, 0, 5);

// ===== HITUNG PERSENTASE PERUBAHAN (simulasi dari data sebelumnya) =====
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
    <title>Dashboard - Admin SMP Al Islam Krian</title>
    <link rel="icon" type="image/png" sizes="64x64" href="../assets/logo/logo-smp-al-islam.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="css/admin-style.css" rel="stylesheet">
    <style>
        .stat-change.negative { color: #EF4444; }
        .stat-change.positive { color: #22C55E; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- ============================================================
        SIDEBAR - Floating Glassmorphism
        ============================================================ -->
        <nav class="admin-sidebar">
            <div class="sidebar-brand">
                <img src="../assets/logo/logo-smp-al-islam.png" alt="Logo SMP Al Islam Krian">
                <div class="brand-text">
                    <span class="school-name">SMP Al Islam</span>
                    <span class="school-tag">Administrator</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="active">
                    <a href="dashboard.php">
                        <i class="bi bi-grid menu-icon"></i>
                        <span class="menu-label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="berita/index.php">
                        <i class="bi bi-newspaper menu-icon"></i>
                        <span class="menu-label">Berita</span>
                        <span class="menu-badge"><?= $beritaCount ?></span>
                    </a>
                </li>
                <li>
                    <a href="guru/index.php">
                        <i class="bi bi-person menu-icon"></i>
                        <span class="menu-label">Guru</span>
                        <span class="menu-badge"><?= $guruCount ?></span>
                    </a>
                </li>
                <li>
                    <a href="prestasi/index.php">
                        <i class="bi bi-trophy menu-icon"></i>
                        <span class="menu-label">Prestasi</span>
                        <span class="menu-badge"><?= $prestasiCount ?></span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-calendar menu-icon"></i>
                        <span class="menu-label">Agenda</span>
                        <span class="menu-badge"><?= $agendaCount ?></span>
                    </a>
                </li>
                
                <li class="menu-divider"></li>
                
                <li>
                    <a href="logout.php" class="text-danger">
                        <i class="bi bi-box-arrow-right menu-icon"></i>
                        <span class="menu-label">Logout</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="user-mini">
                    <div class="avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
                    <div class="user-detail">
                        <div class="name"><?= $_SESSION['nama'] ?></div>
                        <div class="role">Administrator</div>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- ============================================================
        MAIN CONTENT
        ============================================================ -->
        <main class="admin-main">
            <!-- HEADER -->
            <header class="admin-header">
                <div class="header-left">
                    <div class="greeting">
                        <span class="welcome">Selamat datang kembali,</span>
                        <h1 class="title"><?= $_SESSION['nama'] ?> <span>👋</span></h1>
                    </div>
                </div>
                
                <div class="header-right">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" placeholder="Cari sesuatu..." />
                    </div>
                    
                    <button class="action-btn" title="Notifikasi">
                        <i class="bi bi-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    
                    <button class="profile-btn">
                        <div class="avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
                        <div class="profile-info">
                            <div class="name"><?= $_SESSION['nama'] ?></div>
                            <div class="role">Administrator</div>
                        </div>
                        <i class="bi bi-chevron-down" style="font-size:0.8rem;color:var(--text-secondary);"></i>
                    </button>
                </div>
            </header>
            
            <!-- STATISTICS CARDS -->
            <div class="stats-grid">
                <!-- Card 1: Berita -->
                <div class="stat-card green animate-fade-up delay-1">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <span class="stat-change <?= $changeBerita >= 0 ? 'positive' : 'negative' ?>">
                            <i class="bi bi-arrow-<?= $changeBerita >= 0 ? 'up' : 'down' ?>"></i>
                            <?= abs($changeBerita) ?>%
                        </span>
                    </div>
                    <div class="stat-number"><?= $beritaCount ?></div>
                    <div class="stat-label">Total Berita</div>
                </div>
                
                <!-- Card 2: Prestasi -->
                <div class="stat-card gold animate-fade-up delay-2">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <span class="stat-change <?= $changePrestasi >= 0 ? 'positive' : 'negative' ?>">
                            <i class="bi bi-arrow-<?= $changePrestasi >= 0 ? 'up' : 'down' ?>"></i>
                            <?= abs($changePrestasi) ?>%
                        </span>
                    </div>
                    <div class="stat-number"><?= $prestasiCount ?></div>
                    <div class="stat-label">Total Prestasi</div>
                </div>
                
                <!-- Card 3: Guru -->
                <div class="stat-card blue animate-fade-up delay-3">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-person"></i>
                        </div>
                        <span class="stat-change <?= $changeGuru >= 0 ? 'positive' : 'negative' ?>">
                            <i class="bi bi-arrow-<?= $changeGuru >= 0 ? 'up' : 'down' ?>"></i>
                            <?= abs($changeGuru) ?>%
                        </span>
                    </div>
                    <div class="stat-number"><?= $guruCount ?></div>
                    <div class="stat-label">Total Guru</div>
                </div>
                
                <!-- Card 4: Agenda -->
                <div class="stat-card purple animate-fade-up delay-4">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-calendar"></i>
                        </div>
                        <span class="stat-change <?= $changeAgenda >= 0 ? 'positive' : 'negative' ?>">
                            <i class="bi bi-arrow-<?= $changeAgenda >= 0 ? 'up' : 'down' ?>"></i>
                            <?= abs($changeAgenda) ?>%
                        </span>
                    </div>
                    <div class="stat-number"><?= $agendaCount ?></div>
                    <div class="stat-label">Total Agenda</div>
                </div>
            </div>
            
            <!-- CONTENT GRID -->
            <div class="content-grid">
                <!-- Berita Terbaru Table -->
                <div class="glass-card animate-fade-up delay-2">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-clock-history"></i>
                            Berita Terbaru
                        </div>
                        <a href="berita/index.php" class="card-action">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-wrapper">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($beritaList)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;padding:40px 0;color:var(--text-secondary);">
                                            <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                            Belum ada berita
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php $no=1; foreach(array_slice($beritaList,0,5) as $b): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= substr($b['judul'], 0, 45) ?>...</strong></td>
                                        <td>
                                            <span class="status-badge <?= getKategoriClass($b['kategori']) ?>">
                                                <?= getKategoriLabel($b['kategori']) ?>
                                            </span>
                                        </td>
                                        <td><?= formatTanggal($b['tanggal']) ?></td>
                                        <td>
                                            <a href="berita/edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="berita/delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Aktivitas Terbaru -->
                <div class="glass-card animate-fade-up delay-3">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bi bi-activity"></i>
                            Aktivitas Terbaru
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="activity-list">
                            <?php if(empty($aktivitasList)): ?>
                            <div style="text-align:center;padding:30px 0;color:var(--text-secondary);">
                                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                                Belum ada aktivitas
                            </div>
                            <?php else: ?>
                            <?php foreach($aktivitasList as $aktivitas): ?>
                            <div class="activity-item">
                                <div class="activity-icon <?= $aktivitas['icon_color'] ?>">
                                    <i class="bi <?= $aktivitas['icon'] ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text"><?= $aktivitas['text'] ?></div>
                                    <div class="activity-time"><?= $aktivitas['time'] ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Info -->
            <div style="text-align:center;padding:20px 0 0;color:var(--text-secondary);font-size:0.8rem;border-top:1.5px solid var(--border);margin-top:8px;">
                <i class="bi bi-shield-check" style="color:var(--primary);"></i>
                SMP Al Islam Krian &bull; Dashboard Administrator &bull; <?= date('Y') ?>
            </div>
        </main>
    </div>
</body>
</html>