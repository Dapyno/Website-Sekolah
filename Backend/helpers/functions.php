<?php
// helpers/functions.php

function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $t = strtotime($tanggal);
    return date('d', $t) . ' ' . $bulan[(int)date('m', $t)] . ' ' . date('Y', $t);
}

function getKategoriLabel($kategori) {
    $labels = [
        'prestasi' => '🏆 Prestasi',
        'acara' => '🎉 Acara',
        'pendidikan' => '📚 Pendidikan',
        'pengumuman' => '📢 Pengumuman'
    ];
    return $labels[$kategori] ?? $kategori;
}

function getKategoriClass($kategori) {
    $classes = [
        'prestasi' => 'bg-primary',
        'acara' => 'bg-warning',
        'pendidikan' => 'bg-success',
        'pengumuman' => 'bg-danger'
    ];
    return $classes[$kategori] ?? 'bg-secondary';
}

function uploadGambar($file, $targetDir) {
    if($file['error'] != 0) return ['success' => false, 'message' => 'Tidak ada file diupload'];
    
    $check = getimagesize($file['tmp_name']);
    if($check === false) return ['success' => false, 'message' => 'File bukan gambar'];
    
    if($file['size'] > 5000000) return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if(!in_array($ext, $allowed)) return ['success' => false, 'message' => 'Format file tidak diizinkan'];
    
    $newFilename = time() . '_' . uniqid() . '.' . $ext;
    $newPath = $targetDir . $newFilename;
    
    if(move_uploaded_file($file['tmp_name'], $newPath)) {
        return ['success' => true, 'filename' => $newFilename];
    }
    return ['success' => false, 'message' => 'Gagal upload file'];
}

// ===== FUNGSI UNTUK GURU =====
function getStatusBadge($status) {
    if($status == 'active') {
        return '<span class="badge bg-success">Aktif</span>';
    } else {
        return '<span class="badge bg-danger">Non-Aktif</span>';
    }
}

function getJabatanBadge($jabatan) {
    $jabatanLower = strtolower($jabatan);
    if(strpos($jabatanLower, 'kepala sekolah') !== false) {
        return '<span class="badge bg-primary">Kepala Sekolah</span>';
    } elseif(strpos($jabatanLower, 'wakil') !== false) {
        return '<span class="badge bg-warning text-dark">Wakil Kepsek</span>';
    } elseif(strpos($jabatanLower, 'staff') !== false || strpos($jabatanLower, 'administrasi') !== false) {
        return '<span class="badge bg-secondary">Staff</span>';
    } else {
        return '<span class="badge bg-info">Guru</span>';
    }
}
?>