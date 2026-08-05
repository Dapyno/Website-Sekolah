<?php
// ============================================================
// FUNCTIONS.PHP - SMP Al Islam Krian
// Kumpulan fungsi bantuan untuk seluruh sistem
// ============================================================

/**
 * Format tanggal ke format Indonesia
 * @param string $tanggal Format YYYY-MM-DD
 * @return string Tanggal format Indonesia
 */
function formatTanggal($tanggal) {
    if(empty($tanggal) || $tanggal == '0000-00-00') {
        return '-';
    }
    
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $t = strtotime($tanggal);
    if($t === false) {
        return $tanggal;
    }
    
    return date('d', $t) . ' ' . $bulan[(int)date('m', $t)] . ' ' . date('Y', $t);
}

/**
 * Format tanggal dan waktu
 * @param string $tanggal Format YYYY-MM-DD HH:MM:SS
 * @return string
 */
function formatDateTime($tanggal) {
    if(empty($tanggal)) return '-';
    return formatTanggal($tanggal) . ' ' . date('H:i', strtotime($tanggal));
}

/**
 * Potong teks dengan batasan karakter
 * @param string $text Teks yang akan dipotong
 * @param int $limit Batas karakter
 * @param string $suffix Akhiran
 * @return string
 */
function potongTeks($text, $limit = 100, $suffix = '...') {
    if(strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, $limit) . $suffix;
}

/**
 * ============================================================
 * FUNGSI UNTUK BERITA
 * ============================================================
 */

/**
 * Mendapatkan label kategori berita
 * @param string $kategori Kategori berita
 * @return string Label dengan emoji
 */
function getKategoriLabel($kategori) {
    $labels = [
        'prestasi' => '🏆 Prestasi',
        'acara' => '🎉 Acara',
        'pendidikan' => '📚 Pendidikan',
        'pengumuman' => '📢 Pengumuman'
    ];
    return $labels[$kategori] ?? $kategori;
}

/**
 * Mendapatkan class Bootstrap untuk kategori berita
 * @param string $kategori Kategori berita
 * @return string Class Bootstrap
 */
function getKategoriClass($kategori) {
    $classes = [
        'prestasi' => 'bg-primary',
        'acara' => 'bg-warning text-dark',
        'pendidikan' => 'bg-success',
        'pengumuman' => 'bg-danger'
    ];
    return $classes[$kategori] ?? 'bg-secondary';
}

/**
 * Mendapatkan warna kategori berita (CSS)
 * @param string $kategori Kategori berita
 * @return string Warna hex
 */
function getKategoriColor($kategori) {
    $colors = [
        'prestasi' => '#0D6EFD',
        'acara' => '#FFC107',
        'pendidikan' => '#198754',
        'pengumuman' => '#DC3545'
    ];
    return $colors[$kategori] ?? '#6C757D';
}

/**
 * ============================================================
 * FUNGSI UNTUK GURU (REVISI - LEBIH AKURAT)
 * ============================================================
 */

/**
 * Mendapatkan badge status guru
 * @param string $status 'active' atau 'inactive'
 * @return string HTML badge
 */
function getStatusBadge($status) {
    if($status == 'active') {
        return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Aktif</span>';
    } else {
        return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Non-Aktif</span>';
    }
}

/**
 * Mendapatkan badge jabatan guru - REVISI AKURAT
 * @param string $jabatan Nama jabatan
 * @return string HTML badge
 */
function getJabatanBadge($jabatan) {
    $jabatanTrim = trim($jabatan);
    $jabatanLower = strtolower($jabatanTrim);
    
    // ===== URUTAN PENGECEKAN YANG BENAR =====
    // 1. Cek "Kepala Sekolah" (harus sama persis atau tepat)
    if($jabatanLower == 'kepala sekolah' || $jabatanLower == 'kepsek') {
        return '<span class="badge bg-primary"><i class="bi bi-star me-1"></i> ' . htmlspecialchars($jabatanTrim) . '</span>';
    } 
    
    // 2. Cek "Wakil Kepala Sekolah" (harus mengandung "wakil" dan "kepala sekolah")
    elseif(strpos($jabatanLower, 'wakil') !== false && strpos($jabatanLower, 'kepala sekolah') !== false) {
        return '<span class="badge bg-warning text-dark"><i class="bi bi-award me-1"></i> ' . htmlspecialchars($jabatanTrim) . '</span>';
    } 
    
    // 3. Cek "Wakasek" (singkatan)
    elseif($jabatanLower == 'wakasek') {
        return '<span class="badge bg-warning text-dark"><i class="bi bi-award me-1"></i> ' . htmlspecialchars($jabatanTrim) . '</span>';
    }
    
    // 4. Cek "Staff" atau "Administrasi"
    elseif(strpos($jabatanLower, 'staff') !== false || 
           strpos($jabatanLower, 'administrasi') !== false) {
        return '<span class="badge bg-secondary"><i class="bi bi-person me-1"></i> ' . htmlspecialchars($jabatanTrim) . '</span>';
    } 
    
    // 5. Cek "Tenaga Kependidikan"
    elseif(strpos($jabatanLower, 'tenaga kependidikan') !== false) {
        return '<span class="badge bg-info text-dark"><i class="bi bi-person-gear me-1"></i> ' . htmlspecialchars($jabatanTrim) . '</span>';
    } 
    
    // 6. SELAIN ITU ADALAH GURU
    else {
        return '<span class="badge bg-info"><i class="bi bi-book me-1"></i> ' . htmlspecialchars($jabatanTrim) . '</span>';
    }
}

/**
 * Mendapatkan kategori untuk filter guru
 * @param string $jabatan Nama jabatan
 * @return string kategori (kepsek, wakil, staff, guru)
 */
function getGuruCategory($jabatan) {
    $jabatanTrim = trim($jabatan);
    $jabatanLower = strtolower($jabatanTrim);
    
    // 1. Cek Kepala Sekolah
    if($jabatanLower == 'kepala sekolah' || $jabatanLower == 'kepsek') {
        return 'kepsek';
    } 
    
    // 2. Cek Wakil Kepala Sekolah
    elseif(strpos($jabatanLower, 'wakil') !== false && strpos($jabatanLower, 'kepala sekolah') !== false) {
        return 'wakil';
    } 
    
    // 3. Cek Wakasek
    elseif($jabatanLower == 'wakasek') {
        return 'wakil';
    }
    
    // 4. Cek Staff
    elseif(strpos($jabatanLower, 'staff') !== false || 
           strpos($jabatanLower, 'administrasi') !== false) {
        return 'staff';
    } 
    
    // 5. Cek Tenaga Kependidikan
    elseif(strpos($jabatanLower, 'tenaga kependidikan') !== false) {
        return 'staff';
    } 
    
    // 6. SELAIN ITU ADALAH GURU
    else {
        return 'guru';
    }
}

/**
 * Mendapatkan foto guru atau default
 * @param string $foto Nama file foto
 * @param string $default Path foto default
 * @return string Path foto
 */
function getGuruFoto($foto, $default = 'default.jpg') {
    if(!empty($foto) && file_exists('../../assets/guru/' . $foto)) {
        return '../../assets/guru/' . $foto;
    }
    return '../../assets/guru/' . $default;
}

/**
 * ============================================================
 * FUNGSI UNTUK PRESTASI
 * ============================================================
 */

/**
 * Mendapatkan badge tingkat prestasi
 * @param string $tingkat internasional/nasional/provinsi/kabupaten
 * @return string HTML badge
 */
function getTingkatBadge($tingkat) {
    $labels = [
        'internasional' => '<span class="badge bg-danger"><i class="bi bi-globe2 me-1"></i> Internasional</span>',
        'nasional' => '<span class="badge bg-primary"><i class="bi bi-flag me-1"></i> Nasional</span>',
        'provinsi' => '<span class="badge bg-warning text-dark"><i class="bi bi-geo-alt me-1"></i> Provinsi</span>',
        'kabupaten' => '<span class="badge bg-success"><i class="bi bi-house me-1"></i> Kabupaten</span>'
    ];
    return $labels[$tingkat] ?? '<span class="badge bg-secondary">' . htmlspecialchars($tingkat) . '</span>';
}

/**
 * Mendapatkan class CSS untuk tingkat prestasi
 * @param string $tingkat internasional/nasional/provinsi/kabupaten
 * @return string Class CSS
 */
function getTingkatClass($tingkat) {
    $classes = [
        'internasional' => 'internasional',
        'nasional' => 'nasional',
        'provinsi' => 'provinsi',
        'kabupaten' => 'kabupaten'
    ];
    return $classes[$tingkat] ?? '';
}

/**
 * Mendapatkan label tingkat prestasi (tanpa HTML)
 * @param string $tingkat internasional/nasional/provinsi/kabupaten
 * @return string Label
 */
function getTingkatLabel($tingkat) {
    $labels = [
        'internasional' => 'Internasional',
        'nasional' => 'Nasional',
        'provinsi' => 'Provinsi',
        'kabupaten' => 'Kabupaten'
    ];
    return $labels[$tingkat] ?? $tingkat;
}

/**
 * Mendapatkan icon untuk tingkat prestasi
 * @param string $tingkat internasional/nasional/provinsi/kabupaten
 * @return string Icon Bootstrap
 */
function getTingkatIcon($tingkat) {
    $icons = [
        'internasional' => 'bi-globe2',
        'nasional' => 'bi-flag',
        'provinsi' => 'bi-geo-alt',
        'kabupaten' => 'bi-house'
    ];
    return $icons[$tingkat] ?? 'bi-trophy';
}

/**
 * ============================================================
 * FUNGSI UPLOAD FILE
 * ============================================================
 */

/**
 * Upload file gambar
 * @param array $file File dari $_FILES
 * @param string $targetDir Direktori tujuan
 * @return array ['success' => bool, 'filename' => string, 'message' => string]
 */
function uploadGambar($file, $targetDir) {
    // Cek apakah ada file
    if($file['error'] != 0) {
        return ['success' => false, 'message' => 'Tidak ada file diupload'];
    }
    
    // Cek apakah file benar-benar gambar
    $check = getimagesize($file['tmp_name']);
    if($check === false) {
        return ['success' => false, 'message' => 'File bukan gambar'];
    }
    
    // Cek ukuran file (max 5MB)
    if($file['size'] > 5000000) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }
    
    // Cek ekstensi file
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if(!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'Format file tidak diizinkan (gunakan JPG, PNG, WebP)'];
    }
    
    // Buat nama file unik
    $newFilename = time() . '_' . uniqid() . '.' . $ext;
    $newPath = $targetDir . $newFilename;
    
    // Pastikan direktori tujuan ada
    if(!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Upload file
    if(move_uploaded_file($file['tmp_name'], $newPath)) {
        return ['success' => true, 'filename' => $newFilename];
    }
    
    return ['success' => false, 'message' => 'Gagal upload file'];
}

/**
 * Hapus file gambar
 * @param string $filename Nama file
 * @param string $targetDir Direktori file
 * @return bool
 */
function hapusGambar($filename, $targetDir) {
    if(empty($filename) || $filename == 'default.jpg') {
        return true;
    }
    
    $filePath = $targetDir . $filename;
    if(file_exists($filePath)) {
        return unlink($filePath);
    }
    return true;
}

/**
 * ============================================================
 * FUNGSI UTILITY LAINNYA
 * ============================================================
 */

/**
 * Sanitasi input untuk keamanan
 * @param string $input Input yang akan disanitasi
 * @return string Input yang sudah disanitasi
 */
function sanitasi($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate slug dari string
 * @param string $text Teks yang akan dijadikan slug
 * @return string Slug
 */
function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Cek apakah user sudah login
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * ============================================================
 * FUNGSI UNTUK RESPONSIF DAN TAMPILAN
 * ============================================================
 */

/**
 * Mendapatkan badge status untuk berbagai keperluan
 */
function getStatusBadgeLengkap($status, $type = 'guru') {
    $badges = [
        'guru' => [
            'active' => '<span class="badge bg-success">Aktif</span>',
            'inactive' => '<span class="badge bg-danger">Non-Aktif</span>'
        ],
        'pendaftaran' => [
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved' => '<span class="badge bg-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-danger">Ditolak</span>'
        ]
    ];
    
    return $badges[$type][$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Format mata uang Rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

?>