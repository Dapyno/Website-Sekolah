<?php
// Frontend/navbar/prestasi/prestasi.php
require_once '../../../Backend/config/database.php';
require_once '../../../Backend/models/Prestasi.php';
require_once '../../../Backend/helpers/functions.php';

$prestasiModel = new Prestasi($pdo);
$prestasiList = $prestasiModel->getAll();

// Hitung statistik
$totalPrestasi = count($prestasiList);
$totalInternasional = 0;
$totalNasional = 0;
$totalProvinsi = 0;
$totalKabupaten = 0;

foreach($prestasiList as $p) {
    switch($p['tingkat']) {
        case 'internasional': $totalInternasional++; break;
        case 'nasional': $totalNasional++; break;
        case 'provinsi': $totalProvinsi++; break;
        case 'kabupaten': $totalKabupaten++; break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Prestasi SMP Al Islam Krian - Berbagai penghargaan dan pencapaian yang telah diraih oleh siswa dan guru" />
    <title>Prestasi - SMP Al Islam Krian</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="64x64" href="../../../assets/logo/logo-smp-al-islam.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="https://cdn-icons-png.flaticon.com/180/3031/3031515.png" />
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    
    <!-- Custom CSS -->
    <link href="../../../Frontend/css/style.css" rel="stylesheet" />
    <link href="../../../Frontend/css/responsive.css" rel="stylesheet" />
    <link href="../../../Frontend/css/animation.css" rel="stylesheet" />
    <link rel="stylesheet" href="prestasi.css">
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
                <img src="../../../assets/logo/logo-smp-al-islam.png" alt="Logo SMP Al Islam Krian" height="50" class="me-2" />
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
                    <li class="nav-item"><a class="nav-link" href="../guru/guru.php">Guru</a></li>
                    <li class="nav-item"><a class="nav-link active" href="prestasi.php">Prestasi</a></li>
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
    <section class="page-header-prestasi">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../../index.html">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Prestasi</li>
                        </ol>
                    </nav>
                    <h1 class="display-3 fw-bold mb-3">Prestasi <span style="color: var(--gold);">Kami</span></h1>
                    <p class="lead" style="font-size: 1.25rem; opacity: 0.9;">
                        Berbagai penghargaan dan pencapaian yang telah diraih oleh siswa dan guru SMP Al Islam Krian
                    </p>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark me-2 px-4 py-2 rounded-pill">
                            <i class="bi bi-trophy me-1"></i> <?= $totalPrestasi ?>+ Prestasi
                        </span>
                        <span class="badge bg-light text-dark px-4 py-2 rounded-pill">
                            <i class="bi bi-award me-1"></i> <?= $totalNasional + $totalInternasional ?>+ Juara
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block" data-aos="fade-left">
                    <i class="bi bi-trophy-fill" style="font-size: 6rem; opacity: 0.25; color: var(--white);"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTIK PRESTASI -->
    <section class="prestasi-stats">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card-prestasi">
                        <span class="stat-icon"><i class="bi bi-trophy"></i></span>
                        <div class="stat-number"><?= $totalPrestasi ?>+</div>
                        <div class="stat-label">Total Prestasi</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card-prestasi">
                        <span class="stat-icon"><i class="bi bi-award"></i></span>
                        <div class="stat-number"><?= $totalNasional + $totalInternasional ?>+</div>
                        <div class="stat-label">Juara Lomba</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card-prestasi">
                        <span class="stat-icon"><i class="bi bi-globe2"></i></span>
                        <div class="stat-number"><?= $totalInternasional ?>+</div>
                        <div class="stat-label">Prestasi Internasional</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card-prestasi">
                        <span class="stat-icon"><i class="bi bi-star"></i></span>
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Siswa Berprestasi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FILTER PRESTASI -->
    <section class="py-4">
        <div class="container">
            <div class="prestasi-filter" data-aos="fade-up">
                <button class="btn-filter active" data-filter="all">Semua</button>
                <button class="btn-filter" data-filter="internasional">Internasional</button>
                <button class="btn-filter" data-filter="nasional">Nasional</button>
                <button class="btn-filter" data-filter="provinsi">Provinsi</button>
                <button class="btn-filter" data-filter="kabupaten">Kabupaten</button>
            </div>
        </div>
    </section>

    <!-- DAFTAR PRESTASI -->
    <section class="py-3 pb-5">
        <div class="container">
            <div class="row g-4" id="prestasiGrid">
                <?php if(empty($prestasiList)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-trophy" style="font-size:4rem;color:var(--text-soft);opacity:0.3;display:block;margin-bottom:15px;"></i>
                    <h4 style="color:var(--text);">Belum Ada Data Prestasi</h4>
                    <p style="color:var(--text-soft);">Silakan tambahkan data prestasi melalui panel admin.</p>
                </div>
                <?php else: ?>
                <?php foreach($prestasiList as $p): ?>
                <div class="col-md-6 col-lg-4" data-category="<?= $p['tingkat'] ?>" data-aos="fade-up" data-aos-delay="100">
                    <div class="prestasi-card">
                        <div class="prestasi-image">
                            <?php if($p['gambar'] && file_exists('../../../assets/prestasi/' . $p['gambar'])): ?>
                            <img src="../../../assets/prestasi/<?= $p['gambar'] ?>" alt="<?= htmlspecialchars($p['nama_prestasi']) ?>" />
                            <?php else: ?>
                            <img src="../../../assets/prestasi/default.jpg" alt="Default" />
                            <?php endif; ?>
                            <span class="prestasi-badge"><?= getTingkatLabel($p['tingkat']) ?></span>
                            <span class="prestasi-year"><?= $p['tahun'] ?></span>
                        </div>
                        <div class="prestasi-body">
                            <span class="prestasi-level <?= getTingkatClass($p['tingkat']) ?>"><?= getTingkatBadge($p['tingkat']) ?></span>
                            <h5><?= htmlspecialchars($p['nama_prestasi']) ?></h5>
                            <p><?= htmlspecialchars(substr($p['deskripsi'] ?? '', 0, 100)) ?>...</p>
                            <div class="prestasi-meta">
                                <span><i class="bi bi-person"></i> <?= htmlspecialchars($p['siswa'] ?? '-') ?></span>
                                <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($p['lokasi'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- QUOTE PENUTUP -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8 text-center">
                    <i class="bi bi-quote" style="font-size: 3rem; color: var(--primary); opacity: 0.3;"></i>
                    <blockquote class="blockquote fs-4 fst-italic mt-3">
                        "Prestasi bukanlah tujuan akhir, tetapi adalah bukti dari perjalanan panjang yang penuh dengan kerja keras, dedikasi, dan doa."
                    </blockquote>
                    <figcaption class="blockquote-footer mt-2">
                        Dyah Rakhmayanti, S.T., M.Pd. <cite title="Source Title">Kepala Sekolah SMP Al Islam Krian</cite>
                    </figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <h5 class="fw-bold">SMP Al Islam Krian</h5>
                    <p>Sekolah Islam unggulan yang berkomitmen mencetak generasi cerdas, berakhlak mulia, dan berdaya saing global.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h5 class="fw-bold">Informasi Sekolah</h5>
                    <ul class="list-unstyled">
                        <li><a href="../profil/sejarah.html">Sejarah Sekolah</a></li>
                        <li><a href="../profil/visi-misi.html">Visi &amp; Misi</a></li>
                        <li><a href="../profil/sambutan.html">Sambutan Kepsek</a></li>
                        <li><a href="../../../index.html#fasilitas">Fasilitas</a></li>
                    </ul>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h5 class="fw-bold">Layanan &amp; Publikasi</h5>
                    <ul class="list-unstyled">
                        <li><a href="../../../index.html#ppdb">Pendaftaran PPDB</a></li>
                        <li><a href="../../../index.html#agenda">Kalender Akademik</a></li>
                        <li><a href="../berita/berita.php">Arsip Berita</a></li>
                        <li><a href="../../../index.html#galeri">Dokumentasi</a></li>
                    </ul>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h5 class="fw-bold">Kontak</h5>
                    <p><i class="bi bi-geo-alt me-2"></i> Jl. Kyai Mojo No. 18, Jeruk Gamping, Krian, Sidoarjo</p>
                    <p><i class="bi bi-telephone me-2"></i> (031) 1234567</p>
                    <p><i class="bi bi-envelope me-2"></i> info@smpalislankrian.sch.id</p>
                    <div class="live-clock mt-2">
                        <i class="bi bi-clock me-1"></i> <span id="clock">00:00:00</span>
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
    <script src="prestasi.js"></script>

</body>
</html>