<?php
require_once '../../../Backend/config/database.php';
require_once '../../../Backend/helpers/functions.php';

// Ambil data guru dari database dengan urutan yang benar
$stmt = $pdo->query("SELECT * FROM guru ORDER BY 
                     FIELD(jabatan, 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Guru', 'Staff Administrasi', 'Tenaga Kependidikan'),
                     status DESC,
                     nama ASC");
$guruList = $stmt->fetchAll();

// Pisahkan guru aktif dan non-aktif
$guruAktif = [];
$guruNonAktif = [];

foreach($guruList as $g) {
    if(($g['status'] ?? 'active') == 'active') {
        $guruAktif[] = $g;
    } else {
        $guruNonAktif[] = $g;
    }
}

// Gabungkan: aktif dulu, lalu non-aktif
$guruList = array_merge($guruAktif, $guruNonAktif);

// Hitung statistik
$totalGuru = count($guruList);
$totalStaff = 0;
$totalWakil = 0;
$totalKepsek = 0;
$totalTenaga = 0;
$totalNonAktif = count($guruNonAktif);

foreach($guruAktif as $g) {
    $jabatan = trim($g['jabatan']);
    if($jabatan == 'Kepala Sekolah' || strpos(strtolower($jabatan), 'kepala sekolah') !== false) {
        $totalKepsek++;
    } elseif($jabatan == 'Wakil Kepala Sekolah' || strpos(strtolower($jabatan), 'wakil kepala sekolah') !== false) {
        $totalWakil++;
    } elseif($jabatan == 'Tenaga Kependidikan' || strpos(strtolower($jabatan), 'tenaga kependidikan') !== false) {
        $totalTenaga++;
    } elseif($jabatan == 'Staff Administrasi' || strpos(strtolower($jabatan), 'staff') !== false || strpos(strtolower($jabatan), 'administrasi') !== false) {
        $totalStaff++;
    }
}

$totalGuruProfesional = $totalGuru - $totalStaff - $totalWakil - $totalKepsek - $totalTenaga - $totalNonAktif;

// ===== PAGINATION =====
$limit = 9;
$totalPages = ceil(count($guruList) / $limit);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));
$offset = ($currentPage - 1) * $limit;
$guruPerPage = array_slice($guruList, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description"
        content="Daftar lengkap Guru dan Staff SMP Al Islam Krian - Tenaga pendidik profesional yang berdedikasi" />
    <title>Guru & Staff - SMP Al Islam Krian</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="64x64" href="../../../assets/logo/logo-smp-al-islam.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="https://cdn-icons-png.flaticon.com/180/3031/3031515.png" />

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="../../../Frontend/css/style.css" rel="stylesheet" />
    <link href="../../../Frontend/css/responsive.css" rel="stylesheet" />
    <link href="../../../Frontend/css/animation.css" rel="stylesheet" />
    <link href="guru.css" rel="stylesheet" />
</head>

<body>

    <!-- LOADING SCREEN -->
    <div id="loading-screen">
        <div class="loader-wrapper">
            <div class="loader-ring"></div>
            <div class="loader-text">SMP Al Islam Krian</div>
        </div>
    </div>

    <div id="scroll-progress-bar"></div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="../../../index.html">
                <img src="../../../assets/logo/logo-smp-al-islam.png" alt="Logo SMP Al Islam Krian" height="50"
                    class="me-2" />
                <span class="brand-text">SMP Al Islam <span>Krian</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../../../index.html">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProfil" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Profil</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownProfil">
                            <li><a class="dropdown-item" href="../profil/sejarah.html">Sejarah</a></li>
                            <li><a class="dropdown-item" href="../profil/visi-misi.html">Visi &amp; Misi</a></li>
                            <li><a class="dropdown-item" href="../profil/sambutan.html">Sambutan Kepsek</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link active" href="guru.php">Guru</a></li>
                    <li class="nav-item"><a class="nav-link" href="../prestasi/prestasi.php">Prestasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="../berita/berita.php">Berita</a></li>
                    <li class="nav-item"><a class="nav-link" href="../faq/faq.html">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="../kontak/kontak.html">Kontak</a></li>
                </ul>
                <button class="btn btn-dark-mode ms-2" id="darkModeToggle" aria-label="Toggle Dark Mode">
                    <i class="bi bi-moon-fill"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- PAGE HEADER -->
    <section class="page-header-guru">
        <div class="header-decoration">
            <span class="deco-shape shape-1"></span>
            <span class="deco-shape shape-2"></span>
            <span class="deco-shape shape-3"></span>
            <span class="deco-shape shape-4"></span>
        </div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-up" data-aos-duration="1000">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../../index.html">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Guru & Staff</li>
                        </ol>
                    </nav>
                    <h1 class="display-3 fw-bold mb-3">
                        Guru &amp; Staff
                        <span style="color: var(--gold);">Profesional</span>
                    </h1>
                    <p class="lead mb-4" style="font-size: 1.25rem; opacity: 0.9;">
                        Mereka adalah pahlawan tanpa tanda jasa yang membimbing generasi penerus bangsa
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <span class="badge bg-light text-dark px-4 py-2 rounded-pill">
                            <i class="bi bi-people me-2"></i> <?= $totalGuru - $totalNonAktif ?> Guru Aktif
                        </span>
                        <span class="badge bg-light text-dark px-4 py-2 rounded-pill">
                            <i class="bi bi-award me-2"></i> 100% Profesional
                        </span>
                        <span class="badge bg-light text-dark px-4 py-2 rounded-pill">
                            <i class="bi bi-clock-history me-2"></i> 10+ Tahun Pengalaman
                        </span>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block" data-aos="fade-left" data-aos-duration="1000">
                    <div class="header-illustration">
                        <div class="illustration-icon">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="floating-badge badge-1">
                            <i class="bi bi-star-fill text-warning me-1"></i> 100+ Prestasi
                        </div>
                        <div class="floating-badge badge-2">
                            <i class="bi bi-heart-fill text-danger me-1"></i> 500+ Alumni
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTIK GURU -->
    <section class="py-4">
        <div class="container">
            <div class="guru-stats-modern" data-aos="fade-up">
                <div class="row g-4">
                    <div class="col-6 col-lg-3">
                        <div class="stat-card-modern">
                            <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                            <div class="stat-number"><?= $totalGuruProfesional ?></div>
                            <div class="stat-label">Guru</div>
                            <div class="stat-bar"><span style="width: 100%;"></span></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card-modern">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-number"><?= $totalStaff + $totalTenaga ?></div>
                            <div class="stat-label">Staff & TU</div>
                            <div class="stat-bar"><span style="width: 85%;"></span></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card-modern">
                            <div class="stat-icon"><i class="bi bi-star"></i></div>
                            <div class="stat-number"><?= $totalKepsek + $totalWakil ?></div>
                            <div class="stat-label">Pimpinan</div>
                            <div class="stat-bar"><span style="width: 90%;"></span></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card-modern">
                            <div class="stat-icon"><i class="bi bi-award"></i></div>
                            <div class="stat-number">S2</div>
                            <div class="stat-label">Rata-rata Pendidikan</div>
                            <div class="stat-bar"><span style="width: 75%;"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEARCH -->
    <section class="py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchGuru" class="search-input" placeholder="Cari guru berdasarkan nama...">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DAFTAR GURU -->
    <section class="py-4 pb-5" id="daftar-guru">
        <div class="container">
            <div class="row g-4" id="guruGrid">
                <?php if (empty($guruPerPage)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-person-x" style="font-size:4rem;color:var(--text-soft);opacity:0.3;display:block;margin-bottom:15px;"></i>
                        <h4 style="color:var(--text);">Belum Ada Data Guru</h4>
                        <p style="color:var(--text-soft);">Silakan tambahkan data guru melalui panel admin.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($guruPerPage as $guru):
                        // Status badge
                        $statusClass = ($guru['status'] ?? 'active') == 'active' ? 'active' : 'inactive';
                        $statusText = ($guru['status'] ?? 'active') == 'active' ? 'Aktif' : 'Non-Aktif';

                        // Foto
                        $foto = !empty($guru['foto']) && file_exists('../../../assets/guru/' . $guru['foto'])
                            ? '../../../assets/guru/' . $guru['foto']
                            : '../../../assets/guru/default.jpg';
                    ?>
                        <div class="col-md-6 col-lg-4 guru-item" data-nama="<?= strtolower($guru['nama']) ?>" data-aos="fade-up" data-aos-delay="100">
                            <div class="guru-card-detail">
                                <div class="guru-img-wrapper">
                                    <img src="<?= $foto ?>" alt="<?= htmlspecialchars($guru['nama']) ?>" />
                                    <span class="guru-status <?= $statusClass ?>"><?= $statusText ?></span>
                                </div>
                                <div class="guru-info">
                                    <h4><?= htmlspecialchars($guru['nama']) ?></h4>
                                    <div class="guru-jabatan">
                                        <?php
                                        $jabatanLower = strtolower($guru['jabatan']);
                                        if (strpos($jabatanLower, 'kepala sekolah') !== false || strpos($jabatanLower, 'kepsek') !== false) {
                                            echo '<span class="badge bg-primary"><i class="bi bi-star me-1"></i> ' . htmlspecialchars($guru['jabatan']) . '</span>';
                                        } elseif (strpos($jabatanLower, 'wakil') !== false || strpos($jabatanLower, 'wakasek') !== false) {
                                            echo '<span class="badge bg-warning text-dark"><i class="bi bi-award me-1"></i> ' . htmlspecialchars($guru['jabatan']) . '</span>';
                                        } elseif (strpos($jabatanLower, 'staff') !== false || strpos($jabatanLower, 'administrasi') !== false) {
                                            echo '<span class="badge bg-secondary"><i class="bi bi-person me-1"></i> ' . htmlspecialchars($guru['jabatan']) . '</span>';
                                        } elseif (strpos($jabatanLower, 'tenaga kependidikan') !== false) {
                                            echo '<span class="badge bg-info text-dark"><i class="bi bi-person-gear me-1"></i> ' . htmlspecialchars($guru['jabatan']) . '</span>';
                                        } else {
                                            echo '<span class="badge bg-info"><i class="bi bi-book me-1"></i> ' . htmlspecialchars($guru['jabatan']) . '</span>';
                                        }
                                        ?>
                                    </div>
                                    <ul class="guru-detail-list">
                                        <li><i class="bi bi-book"></i> Mapel: <?= htmlspecialchars($guru['mapel'] ?? '-') ?></li>
                                        <li><i class="bi bi-calendar3"></i> Bergabung: <?= htmlspecialchars($guru['tahun_bergabung'] ?? '-') ?></li>
                                        <li><i class="bi bi-award"></i> <?= htmlspecialchars($guru['pendidikan'] ?? '-') ?></li>
                                    </ul>
                                    <div class="guru-social-detail">
                                        <?php if (!empty($guru['instagram'])): ?>
                                            <a href="https://instagram.com/<?= htmlspecialchars($guru['instagram']) ?>" target="_blank" title="Instagram"><i class="bi bi-instagram"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($guru['linkedin'])): ?>
                                            <a href="https://linkedin.com/in/<?= htmlspecialchars($guru['linkedin']) ?>" target="_blank" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                        <?php endif; ?>
                                        <a href="mailto:<?= htmlspecialchars($guru['email'] ?? 'info@smpalislankrian.sch.id') ?>" title="Email"><i class="bi bi-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pesan jika tidak ada hasil pencarian -->
            <div id="searchResult" class="col-12 text-center py-4" style="display:none;">
                <i class="bi bi-search" style="font-size:2rem;color:var(--text-soft);opacity:0.5;display:block;margin-bottom:10px;"></i>
                <p style="color:var(--text-soft);">Tidak ada guru dengan nama yang dicari</p>
            </div>

            <!-- ===== PAGINATION ===== -->
            <?php if($totalPages > 1): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <p class="text-center text-muted small mt-2">
                        Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, count($guruList)) ?> dari <?= count($guruList) ?> guru
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- QUOTE / TESTIMONI GURU -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8 text-center">
                    <i class="bi bi-quote" style="font-size: 3rem; color: var(--primary); opacity: 0.3;"></i>
                    <blockquote class="blockquote fs-4 fst-italic">
                        "Guru terbaik bukanlah mereka yang mengajarkan segalanya, tapi mereka yang menginspirasi
                        muridnya untuk belajar dan berkembang."
                    </blockquote>
                    <figcaption class="blockquote-footer mt-2">
                        Dyah Rakhmayanti, S.T., M.Pd. <cite title="Source Title">Kepala Sekolah SMP Al Islam
                            Krian</cite>
                    </figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Kolom 1: Tentang Sekolah -->
                <div class="col-md-6 col-lg-4">
                    <h5 class="fw-bold">SMP Al Islam Krian</h5>
                    <p>Sekolah Islam unggulan yang berkomitmen mencetak generasi cerdas, berakhlak mulia, dan berdaya
                        saing global.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <!-- Kolom 2: Informasi Sekolah -->
                <div class="col-md-6 col-lg-4">
                    <h5 class="fw-bold">Informasi Sekolah</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.html#fasilitas"><i class="bi bi-chevron-right"></i> Fasilitas</a></li>
                        <li><a href="index.html#akademik"><i class="bi bi-chevron-right"></i> Program Unggulan</a></li>
                        <li><a href="index.html#testimoni"><i class="bi bi-chevron-right"></i> Testimoni Alumni</a></li>
                        <li><a href="index.html#galeri"><i class="bi bi-chevron-right"></i> Dokumentasi</a></li>
                        <li><a href="Frontend/navbar/faq/faq.html"><i class="bi bi-chevron-right"></i> FAQ</a></li>
                        <li><a href="Admin/login.php"><i class="bi bi-chevron-right"></i> Administrator</a>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak + Live Clock + Tanggal Update -->
                <div class="col-md-6 col-lg-4">
                    <h5 class="fw-bold">Kontak</h5>
                    <p><i class="bi bi-geo-alt me-2"></i> Jl. Kyai Mojo No. 18, Jeruk Gamping, Krian, Sidoarjo</p>
                    <p><i class="bi bi-telephone me-2"></i> +62 812-3154-8399</p>
                    <p><i class="bi bi-envelope me-2"></i> admin@smpalislam-krian.sch.id</p>
                    <p><i class="bi bi-clock me-2"></i> Senin - Jumat: 07.00 - 16.00</p>

                    <!-- Live Clock & Tanggal Update -->
                    <div class="footer-info mt-3">
                        <div class="live-clock">
                            <i class="bi bi-clock me-1"></i> <span id="clock">00:00:00</span>
                        </div>
                        <div class="update-date mt-2">
                            <i class="bi bi-calendar3 me-1"></i> <span id="updateDate">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-4" />
            <div class="text-center">
                <p class="mb-0">&copy; 2026 SMP Al Islam Krian. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- FLOATING WHATSAPP -->
    <a href="https://wa.me/6281234567890" target="_blank" class="floating-whatsapp" aria-label="WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <!-- BACK TO TOP -->
    <button id="backToTop" class="btn btn-primary back-to-top" aria-label="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../../Frontend/js/script.js"></script>
    <script src="../../../Frontend/js/counter.js"></script>
    <script src="guru.js"></script>

</body>
</html>