<?php
// models/Guru.php
class Guru {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * CREATE - Tambah guru baru
     */
    public function create($data) {
        $sql = "INSERT INTO guru (nama, jabatan, mapel, foto, status, tahun_bergabung, pendidikan, instagram, linkedin, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama'],
            $data['jabatan'],
            $data['mapel'] ?? '',
            $data['foto'] ?? '',
            $data['status'] ?? 'active',
            $data['tahun_bergabung'] ?? '',
            $data['pendidikan'] ?? '',
            $data['instagram'] ?? '',
            $data['linkedin'] ?? '',
            $data['email'] ?? ''
        ]);
    }
    
    /**
     * READ - Ambil semua guru dengan urutan prioritas jabatan
     */
    public function getAll() {
        $sql = "SELECT * FROM guru ORDER BY 
                FIELD(jabatan, 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Guru', 'Staff Administrasi', 'Tenaga Kependidikan'),
                status DESC,
                nama ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil semua guru aktif
     */
    public function getActive() {
        $sql = "SELECT * FROM guru WHERE status = 'active' ORDER BY 
                FIELD(jabatan, 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Guru', 'Staff Administrasi', 'Tenaga Kependidikan'),
                nama ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil guru berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM guru WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * READ - Ambil guru berdasarkan jabatan
     */
    public function getByJabatan($jabatan) {
        $stmt = $this->pdo->prepare("SELECT * FROM guru WHERE jabatan LIKE ? AND status = 'active' ORDER BY nama ASC");
        $stmt->execute(['%' . $jabatan . '%']);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil Kepala Sekolah
     */
    public function getKepalaSekolah() {
        $stmt = $this->pdo->prepare("SELECT * FROM guru WHERE jabatan LIKE '%Kepala Sekolah%' AND status = 'active' LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * READ - Ambil guru berdasarkan mapel
     */
    public function getByMapel($mapel) {
        $stmt = $this->pdo->prepare("SELECT * FROM guru WHERE mapel LIKE ? AND status = 'active' ORDER BY nama ASC");
        $stmt->execute(['%' . $mapel . '%']);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil guru terbaru dengan limit
     */
    public function getLatest($limit = 6) {
        $stmt = $this->pdo->prepare("SELECT * FROM guru WHERE status = 'active' ORDER BY id DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * UPDATE - Edit data guru
     */
    public function update($id, $data) {
        $sql = "UPDATE guru SET 
                nama = ?, 
                jabatan = ?, 
                mapel = ?, 
                foto = ?, 
                status = ?, 
                tahun_bergabung = ?, 
                pendidikan = ?, 
                instagram = ?, 
                linkedin = ?, 
                email = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama'],
            $data['jabatan'],
            $data['mapel'] ?? '',
            $data['foto'] ?? '',
            $data['status'] ?? 'active',
            $data['tahun_bergabung'] ?? '',
            $data['pendidikan'] ?? '',
            $data['instagram'] ?? '',
            $data['linkedin'] ?? '',
            $data['email'] ?? '',
            $id
        ]);
    }
    
    /**
     * DELETE - Hapus guru beserta foto
     */
    public function delete($id) {
        // Ambil data guru untuk hapus foto
        $guru = $this->getById($id);
        if ($guru && !empty($guru['foto']) && $guru['foto'] != 'default.jpg') {
            $fotoPath = __DIR__ . '/../../assets/guru/' . $guru['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM guru WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * STATISTIK - Hitung total guru
     */
    public function getTotal() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM guru");
        return $stmt->fetch()['total'] ?? 0;
    }
    
    /**
     * STATISTIK - Hitung total guru aktif
     */
    public function getTotalActive() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM guru WHERE status = 'active'");
        return $stmt->fetch()['total'] ?? 0;
    }
    
    /**
     * STATISTIK - Hitung total guru non-aktif
     */
    public function getTotalInactive() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM guru WHERE status = 'inactive'");
        return $stmt->fetch()['total'] ?? 0;
    }
    
    /**
     * STATISTIK - Jumlah guru per jabatan
     */
    public function countByJabatan() {
        $stmt = $this->pdo->query("SELECT jabatan, COUNT(*) as total FROM guru GROUP BY jabatan ORDER BY total DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * STATISTIK - Jumlah guru per status
     */
    public function countByStatus() {
        $stmt = $this->pdo->query("SELECT status, COUNT(*) as total FROM guru GROUP BY status");
        $results = $stmt->fetchAll();
        
        $stats = ['active' => 0, 'inactive' => 0];
        foreach ($results as $row) {
            $stats[$row['status']] = $row['total'];
        }
        return $stats;
    }
    
    /**
     * STATISTIK - Dapatkan daftar jabatan unik
     */
    public function getJabatanList() {
        $stmt = $this->pdo->query("SELECT DISTINCT jabatan FROM guru ORDER BY jabatan ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * STATISTIK - Dapatkan daftar mapel unik
     */
    public function getMapelList() {
        $stmt = $this->pdo->query("SELECT DISTINCT mapel FROM guru WHERE mapel != '' ORDER BY mapel ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * SEARCH - Cari guru berdasarkan keyword
     */
    public function search($keyword) {
        $sql = "SELECT * FROM guru 
                WHERE nama LIKE ? 
                OR jabatan LIKE ? 
                OR mapel LIKE ? 
                OR pendidikan LIKE ?
                ORDER BY FIELD(jabatan, 'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Guru', 'Staff Administrasi', 'Tenaga Kependidikan'),
                nama ASC";
        $stmt = $this->pdo->prepare($sql);
        $like = '%' . $keyword . '%';
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }
    
    /**
     * BULK - Update status guru (aktif/non-aktif)
     */
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE guru SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
?>