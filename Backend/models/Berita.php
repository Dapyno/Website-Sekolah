<?php
// models/Berita.php
class Berita {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * CREATE - Tambah berita baru
     */
    public function create($data) {
        $sql = "INSERT INTO berita (judul, kategori, konten, gambar, tanggal) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['judul'],
            $data['kategori'] ?? 'pendidikan',
            $data['konten'],
            $data['gambar'] ?? '',
            $data['tanggal'] ?? date('Y-m-d')
        ]);
    }
    
    /**
     * READ - Ambil semua berita
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil berita berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * READ - Ambil berita berdasarkan kategori
     */
    public function getByKategori($kategori) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita WHERE kategori = ? ORDER BY tanggal DESC, id DESC");
        $stmt->execute([$kategori]);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil berita terbaru dengan limit
     */
    public function getLatest($limit = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita ORDER BY tanggal DESC, id DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil berita featured (terbaru pertama)
     */
    public function getFeatured() {
        $stmt = $this->pdo->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC LIMIT 1");
        return $stmt->fetch();
    }
    
    /**
     * READ - Ambil berita berdasarkan tahun
     */
    public function getByTahun($tahun) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita WHERE YEAR(tanggal) = ? ORDER BY tanggal DESC, id DESC");
        $stmt->execute([$tahun]);
        return $stmt->fetchAll();
    }
    
    /**
     * READ - Ambil berita berdasarkan rentang tanggal
     */
    public function getByDateRange($startDate, $endDate) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal DESC, id DESC");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    /**
     * UPDATE - Update data berita
     */
    public function update($id, $data) {
        $sql = "UPDATE berita SET 
                judul = ?, 
                kategori = ?, 
                konten = ?, 
                gambar = ?, 
                tanggal = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['judul'],
            $data['kategori'] ?? 'pendidikan',
            $data['konten'],
            $data['gambar'] ?? '',
            $data['tanggal'] ?? date('Y-m-d'),
            $id
        ]);
    }
    
    /**
     * DELETE - Hapus berita beserta gambar
     */
    public function delete($id) {
        // Ambil data berita untuk hapus gambar
        $berita = $this->getById($id);
        if ($berita && !empty($berita['gambar']) && $berita['gambar'] != 'default.jpg') {
            $gambarPath = __DIR__ . '/../../assets/berita/' . $berita['gambar'];
            if (file_exists($gambarPath)) {
                unlink($gambarPath);
            }
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM berita WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * STATISTIK - Hitung total berita
     */
    public function getTotal() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM berita");
        return $stmt->fetch()['total'] ?? 0;
    }
    
    /**
     * STATISTIK - Hitung berita per kategori
     */
    public function countByKategori() {
        $stmt = $this->pdo->query("SELECT kategori, COUNT(*) as total FROM berita GROUP BY kategori");
        $results = $stmt->fetchAll();
        
        $stats = [
            'prestasi' => 0,
            'acara' => 0,
            'pendidikan' => 0,
            'pengumuman' => 0
        ];
        
        foreach ($results as $row) {
            $stats[$row['kategori']] = $row['total'];
        }
        
        return $stats;
    }
    
    /**
     * STATISTIK - Hitung berita per bulan
     */
    public function countByBulan($tahun = null) {
        $tahun = $tahun ?? date('Y');
        $stmt = $this->pdo->prepare("
            SELECT MONTH(tanggal) as bulan, COUNT(*) as total 
            FROM berita 
            WHERE YEAR(tanggal) = ? 
            GROUP BY MONTH(tanggal) 
            ORDER BY bulan ASC
        ");
        $stmt->execute([$tahun]);
        return $stmt->fetchAll();
    }
    
    /**
     * STATISTIK - Dapatkan daftar tahun yang ada
     */
    public function getTahunList() {
        $stmt = $this->pdo->query("SELECT DISTINCT YEAR(tanggal) as tahun FROM berita ORDER BY tahun DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * STATISTIK - Dapatkan daftar kategori unik
     */
    public function getKategoriList() {
        $stmt = $this->pdo->query("SELECT DISTINCT kategori FROM berita ORDER BY kategori ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * SEARCH - Cari berita berdasarkan keyword
     */
    public function search($keyword) {
        $sql = "SELECT * FROM berita 
                WHERE judul LIKE ? 
                OR konten LIKE ? 
                OR kategori LIKE ?
                ORDER BY tanggal DESC, id DESC";
        $stmt = $this->pdo->prepare($sql);
        $like = '%' . $keyword . '%';
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }
    
    /**
     * PAGINATION - Ambil berita dengan pagination
     */
    public function getPaginated($limit = 10, $offset = 0) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita ORDER BY tanggal DESC, id DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * GET - Ambil berita sebelumnya (untuk navigasi detail)
     */
    public function getPrevious($id, $tanggal) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM berita 
            WHERE tanggal < ? OR (tanggal = ? AND id < ?) 
            ORDER BY tanggal DESC, id DESC LIMIT 1
        ");
        $stmt->execute([$tanggal, $tanggal, $id]);
        return $stmt->fetch();
    }
    
    /**
     * GET - Ambil berita berikutnya (untuk navigasi detail)
     */
    public function getNext($id, $tanggal) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM berita 
            WHERE tanggal > ? OR (tanggal = ? AND id > ?) 
            ORDER BY tanggal ASC, id ASC LIMIT 1
        ");
        $stmt->execute([$tanggal, $tanggal, $id]);
        return $stmt->fetch();
    }
}
?>