<?php
class Berita {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($data) {
        $sql = "INSERT INTO berita (judul, kategori, konten, gambar, tanggal) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['judul'], $data['kategori'], $data['konten'], $data['gambar'], $data['tanggal']]);
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE berita SET judul = ?, kategori = ?, konten = ?, gambar = ?, tanggal = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['judul'], $data['kategori'], $data['konten'], $data['gambar'], $data['tanggal'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM berita WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function countByCategory() {
        $stmt = $this->pdo->query("SELECT kategori, COUNT(*) as total FROM berita GROUP BY kategori");
        return $stmt->fetchAll();
    }
}
?>