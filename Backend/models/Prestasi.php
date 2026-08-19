<?php
// models/Prestasi.php
class Prestasi {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * CREATE - Tambah prestasi baru
     */
    public function create($data) {
        $sql = "INSERT INTO prestasi (nama_prestasi, tingkat, tahun, deskripsi, gambar, siswa, lokasi) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama_prestasi'],
            $data['tingkat'],
            $data['tahun'],
            $data['deskripsi'] ?? '',
            $data['gambar'] ?? '',
            $data['siswa'] ?? '',
            $data['lokasi'] ?? ''
        ]);
    }
    
    /**
     * READ - Ambil semua prestasi
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM prestasi ORDER BY tahun DESC, id DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil prestasi berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM prestasi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * READ - Ambil prestasi berdasarkan tingkat
     */
    public function getByTingkat($tingkat) {
        $stmt = $this->pdo->prepare("SELECT * FROM prestasi WHERE tingkat = ? ORDER BY tahun DESC, id DESC");
        $stmt->execute([$tingkat]);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil prestasi terbaru dengan limit
     */
    public function getLatest($limit = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM prestasi ORDER BY tahun DESC, id DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil prestasi berdasarkan tahun
     */
    public function getByTahun($tahun) {
        $stmt = $this->pdo->prepare("SELECT * FROM prestasi WHERE tahun = ? ORDER BY id DESC");
        $stmt->execute([$tahun]);
        return $stmt->fetchAll();
    }
    
    /**
     * UPDATE - Update data prestasi
     */
    public function update($id, $data) {
        $sql = "UPDATE prestasi SET 
                nama_prestasi = ?, 
                tingkat = ?, 
                tahun = ?, 
                deskripsi = ?, 
                gambar = ?, 
                siswa = ?, 
                lokasi = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama_prestasi'],
            $data['tingkat'],
            $data['tahun'],
            $data['deskripsi'] ?? '',
            $data['gambar'] ?? '',
            $data['siswa'] ?? '',
            $data['lokasi'] ?? '',
            $id
        ]);
    }
    
    /**
     * DELETE - Hapus prestasi
     */
    public function delete($id) {
        // Ambil data prestasi untuk hapus gambar
        $prestasi = $this->getById($id);
        if ($prestasi && !empty($prestasi['gambar']) && $prestasi['gambar'] != 'default.jpg') {
            $gambarPath = __DIR__ . '/../../assets/prestasi/' . $prestasi['gambar'];
            if (file_exists($gambarPath)) {
                unlink($gambarPath);
            }
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM prestasi WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * STATISTIK - Hitung total prestasi
     */
    public function getTotal() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM prestasi");
        return $stmt->fetch()['total'] ?? 0;
    }
    
    /**
     * STATISTIK - Hitung prestasi berdasarkan tingkat
     */
    public function countByTingkat() {
        $stmt = $this->pdo->query("SELECT tingkat, COUNT(*) as total FROM prestasi GROUP BY tingkat");
        $results = $stmt->fetchAll();
        
        // Format hasil
        $stats = [
            'internasional' => 0,
            'nasional' => 0,
            'provinsi' => 0,
            'kabupaten' => 0
        ];
        
        foreach ($results as $row) {
            $stats[$row['tingkat']] = $row['total'];
        }
        
        return $stats;
    }
    
    /**
     * STATISTIK - Hitung prestasi berdasarkan tahun
     */
    public function countByTahun() {
        $stmt = $this->pdo->query("SELECT tahun, COUNT(*) as total FROM prestasi GROUP BY tahun ORDER BY tahun DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * STATISTIK - Dapatkan tahun-tahun yang ada
     */
    public function getTahunList() {
        $stmt = $this->pdo->query("SELECT DISTINCT tahun FROM prestasi ORDER BY tahun DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * SEARCH - Cari prestasi berdasarkan keyword
     */
    public function search($keyword) {
        $sql = "SELECT * FROM prestasi 
                WHERE nama_prestasi LIKE ? 
                OR deskripsi LIKE ? 
                OR siswa LIKE ? 
                OR lokasi LIKE ?
                ORDER BY tahun DESC, id DESC";
        $stmt = $this->pdo->prepare($sql);
        $like = '%' . $keyword . '%';
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }
}
?>