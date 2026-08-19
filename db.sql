
-- DATABASE: smp_islam_watestanjung

CREATE DATABASE IF NOT EXISTS smp_islam_watestanjung;
USE smp_islam_watestanjung;

-- TABEL: users (untuk login admin)
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin', 'superadmin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (bisa diubah atau dihapus)
INSERT INTO users (username, password, nama, role) VALUES
('admin', 'admin123', 'Administrator', 'superadmin');

-- TABEL: berita
CREATE TABLE IF NOT EXISTS berita (
    id INT(11) NOT NULL AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    kategori ENUM('prestasi', 'acara', 'pendidikan', 'pengumuman') DEFAULT 'pendidikan',
    konten TEXT NOT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: guru
CREATE TABLE IF NOT EXISTS guru (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    mapel VARCHAR(100) DEFAULT NULL,
    pendidikan VARCHAR(100) DEFAULT NULL,
    tahun_bergabung VARCHAR(10) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    instagram VARCHAR(100) DEFAULT NULL,
    linkedin VARCHAR(100) DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: prestasi
CREATE TABLE IF NOT EXISTS prestasi (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama_prestasi VARCHAR(255) NOT NULL,
    tingkat ENUM('internasional', 'nasional', 'provinsi', 'kabupaten') NOT NULL,
    tahun YEAR(4) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    gambar VARCHAR(255) DEFAULT NULL,
    siswa VARCHAR(100) DEFAULT NULL,
    lokasi VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: agenda (untuk kegiatan/agenda sekolah)
CREATE TABLE IF NOT EXISTS agenda (
    id INT(11) NOT NULL AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    tanggal DATE NOT NULL,
    waktu VARCHAR(50) DEFAULT NULL,
    lokasi VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: ekstrakurikuler
CREATE TABLE IF NOT EXISTS ekstrakurikuler (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    kategori ENUM('keagamaan', 'olahraga', 'seni', 'sains') NOT NULL,
    deskripsi TEXT NOT NULL,
    jadwal VARCHAR(100) DEFAULT NULL,
    peserta INT(11) DEFAULT 0,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: testimoni (untuk testimoni alumni)
CREATE TABLE IF NOT EXISTS testimoni (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    pekerjaan VARCHAR(100) DEFAULT NULL,
    testimoni TEXT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    rating INT(1) DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: pengunjung (untuk statistik pengunjung website)
CREATE TABLE IF NOT EXISTS pengunjung (
    id INT(11) NOT NULL AUTO_INCREMENT,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    halaman VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: kontak (untuk menyimpan pesan dari form kontak)
CREATE TABLE IF NOT EXISTS kontak (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subjek VARCHAR(255) DEFAULT NULL,
    pesan TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABEL: setting (untuk konfigurasi website)
CREATE TABLE IF NOT EXISTS setting (
    id INT(11) NOT NULL AUTO_INCREMENT,
    key_setting VARCHAR(100) NOT NULL UNIQUE,
    value_setting TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO setting (key_setting, value_setting) VALUES
('nama_sekolah', 'SMP Islam Watestanjung'),
('alamat', 'Jl. Kyai Mojo No. 18, Jeruk Gamping, Watestanjung, Sidoarjo 61262'),
('telepon', '+62 812-3154-8399'),
('email', 'admin@smpislamwatestanjung.sch.id'),
('tahun_berdiri', '1998'),
('deskripsi', 'Sekolah Islam unggulan yang berkomitmen mencetak generasi cerdas, berakhlak mulia, dan berdaya saing global.'),
('akreditasi', 'A'),
('curriculum', 'Kurikulum Merdeka'),
('visi', 'Terwujudnya generasi muslim yang cerdas, berakhlak mulia, dan berdaya saing global berdasarkan Al-Qur\'an dan Sunnah.'),
('misi', '1. Menyelenggarakan pendidikan berkualitas dengan kurikulum nasional dan agama\n2. Membentuk karakter siswa dengan nilai-nilai Islam dan kebangsaan\n3. Mengembangkan potensi akademik dan non-akademik siswa\n4. Mewujudkan lingkungan belajar yang inovatif dan menyenangkan\n5. Menjalin kerjasama dengan orang tua dan masyarakat');

-- VIEW: dashboard_statistik (untuk memudahkan dashboard admin)
CREATE OR REPLACE VIEW dashboard_statistik AS
SELECT 
    (SELECT COUNT(*) FROM berita) AS total_berita,
    (SELECT COUNT(*) FROM guru) AS total_guru,
    (SELECT COUNT(*) FROM prestasi) AS total_prestasi,
    (SELECT COUNT(*) FROM agenda) AS total_agenda,
    (SELECT COUNT(*) FROM ekstrakurikuler) AS total_ekstrakurikuler,
    (SELECT COUNT(*) FROM testimoni) AS total_testimoni,
    (SELECT COUNT(*) FROM kontak WHERE status = 'unread') AS pesan_belum_dibaca;