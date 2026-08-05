<?php
// models/Prestasi.php
class Prestasi {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // CREATE
    public function create($data) {
        $sql = "INSERT INTO prestasi (nama_prestasi, tingkat, tahun, deskripsi, gambar, siswa, lokasi) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama_prestasi'],
            $data['tingkat'],
            $data['tahun'],
            $data['deskripsi'],
            $data['gambar'],
            $data['siswa'],
            $data['lokasi']
        ]);
    }
    
    // READ - Ambil semua prestasi
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM prestasi ORDER BY tahun DESC, id DESC");
        return $stmt->fetchAll();
    }
    
    // READ - Ambil prestasi berdasarkan ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM prestasi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // UPDATE
    public function update($id, $data) {
        $sql = "UPDATE prestasi SET 
                nama_prestasi = ?, tingkat = ?, tahun = ?, deskripsi = ?, 
                gambar = ?, siswa = ?, lokasi = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama_prestasi'],
            $data['tingkat'],
            $data['tahun'],
            $data['deskripsi'],
            $data['gambar'],
            $data['siswa'],
            $data['lokasi'],
            $id
        ]);
    }
    
    // DELETE
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM prestasi WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // COUNT by tingkat
    public function countByTingkat() {
        $stmt = $this->pdo->query("SELECT tingkat, COUNT(*) as total FROM prestasi GROUP BY tingkat");
        return $stmt->fetchAll();
    }
}
?>