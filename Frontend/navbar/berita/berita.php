<?php
require_once __DIR__ . '/../../../Backend/config/database.php';
require_once __DIR__ . '/../../../Backend/models/Berita.php';
require_once __DIR__ . '/../../../Backend/helpers/functions.php';

$beritaModel = new Berita($pdo);
$beritaList = $beritaModel->getAll();

// Pisahkan berita featured (pertama) dan sisanya
$featured = !empty($beritaList) ? array_shift($beritaList) : null;
$remainingBerita = $beritaList;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Berita SMP Al Islam Krian - Informasi terkini seputar kegiatan, prestasi, dan pengumuman sekolah" />
    <title>Berita - SMP Al Islam Krian</title>
    
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
    <link href="berita.css" rel="stylesheet" />
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

    <!-- ==================== NAVBAR ==================== -->
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
                    <li class="nav-item"><a class="nav-link" href="../prestasi/prestasi.html">Prestasi</a></li>
                    <li class="nav-item"><a class="nav-link active" href="berita.php">Berita</a></li>
                    <li class="nav-item"><a class="nav-link" href="../faq/faq.html">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="../kontak/kontak.html">Kontak</a></li>
                </ul>
                <button class="btn btn-dark-mode ms-2" id="darkModeToggle" aria-label="Toggle Dark Mode">
                    <i class="bi bi-moon-fill"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- ==================== PAGE HEADER ==================== -->
    <section class="page-header-berita">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../../index.html">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Berita</li>
                        </ol>
                    </nav>
                    <h1 class="display-3 fw-bold mb-3">Berita <span style="color: var(--gold);">Terkini</span></h1>
                    <p class="lead" style="font-size: 1.25rem; opacity: 0.9;">
                        Informasi terbaru seputar kegiatan, prestasi, dan pengumuman dari SMP Al Islam Krian
                    </p>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark me-2 px-4 py-2 rounded-pill">
                            <i class="bi bi-newspaper me-1"></i> <?= count($beritaList) + ($featured ? 1 : 0) ?>+ Berita
                        </span>
                        <span class="badge bg-light text-dark px-4 py-2 rounded-pill">
                            <i class="bi bi-calendar3 me-1"></i> Update Terkini
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block" data-aos="fade-left">
                    <i class="bi bi-newspaper" style="font-size: 6rem; opacity: 0.25; color: var(--white);"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== STATISTIK BERITA ==================== -->
    <section class="berita-stats">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card-berita">
                        <span class="stat-icon"><i class="bi bi-newspaper"></i></span>
                        <div class="stat-number"><?= count($beritaList) + ($featured ? 1 : 0) ?>+</div>
                        <div class="stat-label">Total Berita</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card-berita">
                        <span class="stat-icon"><i class="bi bi-trophy"></i></span>
                        <div class="stat-number">
                            <?php 
                            $countPrestasi = 0;
                            foreach($beritaList as $b) {
                                if($b['kategori'] == 'prestasi') $countPrestasi++;
                            }
                            if($featured && $featured['kategori'] == 'prestasi') $countPrestasi++;
                            echo $countPrestasi . '+';
                            ?>
                        </div>
                        <div class="stat-label">Berita Prestasi</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card-berita">
                        <span class="stat-icon"><i class="bi bi-calendar-event"></i></span>
                        <div class="stat-number">
                            <?php 
                            $countAcara = 0;
                            foreach($beritaList as $b) {
                                if($b['kategori'] == 'acara') $countAcara++;
                            }
                            if($featured && $featured['kategori'] == 'acara') $countAcara++;
                            echo $countAcara . '+';
                            ?>
                        </div>
                        <div class="stat-label">Berita Acara</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card-berita">
                        <span class="stat-icon"><i class="bi bi-megaphone"></i></span>
                        <div class="stat-number">
                            <?php 
                            $countPengumuman = 0;
                            foreach($beritaList as $b) {
                                if($b['kategori'] == 'pengumuman') $countPengumuman++;
                            }
                            if($featured && $featured['kategori'] == 'pengumuman') $countPengumuman++;
                            echo $countPengumuman . '+';
                            ?>
                        </div>
                        <div class="stat-label">Pengumuman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FILTER BERITA ==================== -->
    <section class="py-4">
        <div class="container">
            <div class="berita-filter" data-aos="fade-up">
                <button class="btn-filter active" data-filter="all">Semua</button>
                <button class="btn-filter" data-filter="prestasi">Prestasi</button>
                <button class="btn-filter" data-filter="acara">Acara</button>
                <button class="btn-filter" data-filter="pendidikan">Pendidikan</button>
                <button class="btn-filter" data-filter="pengumuman">Pengumuman</button>
            </div>
        </div>
    </section>

    <!-- ==================== DAFTAR BERITA ==================== -->
    <section class="py-3 pb-5">
        <div class="container">
            
            <?php if($featured): ?>
            <div class="berita-featured" data-aos="fade-up" data-aos-delay="100">
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="berita-featured-image">
                            <?php if($featured['gambar']): ?>
                            <img src="../../../assets/berita/<?= $featured['gambar'] ?>" alt="<?= htmlspecialchars($featured['judul']) ?>" />
                            <?php else: ?>
                            <img src="../../../assets/berita/default.jpg" alt="Default" />
                            <?php endif; ?>
                            <span class="berita-badge">Featured</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="berita-featured-body">
                            <div class="berita-date">
                                <i class="bi bi-calendar3"></i> <?= formatTanggal($featured['tanggal']) ?>
                            </div>
                            <h3><?= htmlspecialchars($featured['judul']) ?></h3>
                            <p><?= substr(strip_tags($featured['konten']), 0, 180) ?>...</p>
                            <a href="detail.php?id=<?= $featured['id'] ?>" class="btn-readmore">Baca Selengkapnya <i class="bi bi-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(!empty($remainingBerita)): ?>
            <div class="row g-4" id="beritaGrid">
                <?php foreach($remainingBerita as $b): ?>
                <div class="col-md-6 col-lg-4" data-category="<?= $b['kategori'] ?>" data-aos="fade-up" data-aos-delay="200">
                    <div class="berita-card-full">
                        <div class="berita-image">
                            <?php if($b['gambar']): ?>
                            <img src="../../../assets/berita/<?= $b['gambar'] ?>" alt="<?= htmlspecialchars($b['judul']) ?>" />
                            <?php else: ?>
                            <img src="../../../assets/berita/default.jpg" alt="Default" />
                            <?php endif; ?>
                            <span class="berita-badge"><?= getKategoriLabel($b['kategori']) ?></span>
                            <span class="berita-date"><i class="bi bi-calendar3"></i> <?= formatTanggal($b['tanggal']) ?></span>
                        </div>
                        <div class="berita-body">
                            <span class="berita-category <?= $b['kategori'] ?>"><?= getKategoriLabel($b['kategori']) ?></span>
                            <h5><?= htmlspecialchars($b['judul']) ?></h5>
                            <p><?= substr(strip_tags($b['konten']), 0, 100) ?>...</p>
                            <a href="detail.php?id=<?= $b['id'] ?>" class="btn-readmore">Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-berita">
                <i class="bi bi-newspaper"></i>
                <h4>Belum Ada Berita</h4>
                <p>Belum ada berita yang dipublikasikan. Silakan cek kembali nanti.</p>
            </div>
            <?php endif; ?>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="#" class="btn btn-outline-primary btn-lg px-5 py-3 ripple">
                    Muat Lebih Banyak <i class="bi bi-arrow-down ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== QUOTE ==================== -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8 text-center">
                    <i class="bi bi-quote" style="font-size: 3rem; color: var(--primary); opacity: 0.3;"></i>
                    <blockquote class="blockquote fs-4 fst-italic mt-3">
                        "Berita adalah jendela informasi yang menghubungkan sekolah dengan masyarakat. Melalui berita, kita berbagi cerita, inspirasi, dan prestasi."
                    </blockquote>
                    <figcaption class="blockquote-footer mt-2">
                        Dyah Rakhmayanti, S.T., M.Pd. <cite title="Source Title">Kepala Sekolah SMP Al Islam Krian</cite>
                    </figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
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
                        <li><a href="berita.php">Arsip Berita</a></li>
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

    <a href="https://wa.me/6281234567890" target="_blank" class="floating-whatsapp" aria-label="WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <button id="backToTop" class="btn btn-primary back-to-top" aria-label="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../../Frontend/js/script.js"></script>
    <script src="../../../Frontend/js/counter.js"></script>
    <script src="berita.js"></script>
</body>
</html>