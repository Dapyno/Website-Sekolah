<?php
class Berita {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
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
    
    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO berita (judul, kategori, konten, gambar, tanggal) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['judul'], $data['kategori'], $data['konten'], $data['gambar'], $data['tanggal']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE berita SET judul = ?, kategori = ?, konten = ?, gambar = ?, tanggal = ? WHERE id = ?");
        return $stmt->execute([$data['judul'], $data['kategori'], $data['konten'], $data['gambar'], $data['tanggal'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM berita WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>