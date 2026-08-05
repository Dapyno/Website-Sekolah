<?php
// models/Guru.php
class Guru {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // CREATE - Tambah guru
    public function create($data) {
        $sql = "INSERT INTO guru (nama, jabatan, mapel, foto, status, tahun_bergabung, pendidikan, instagram, linkedin, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama'],
            $data['jabatan'],
            $data['mapel'],
            $data['foto'],
            $data['status'],
            $data['tahun_bergabung'],
            $data['pendidikan'],
            $data['instagram'],
            $data['linkedin'],
            $data['email']
        ]);
    }
    
    // READ - Ambil semua guru
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM guru ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    // READ - Ambil guru berdasarkan ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM guru WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // UPDATE - Edit guru
    public function update($id, $data) {
        $sql = "UPDATE guru SET 
                nama = ?, jabatan = ?, mapel = ?, foto = ?, status = ?, 
                tahun_bergabung = ?, pendidikan = ?, instagram = ?, linkedin = ?, email = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama'],
            $data['jabatan'],
            $data['mapel'],
            $data['foto'],
            $data['status'],
            $data['tahun_bergabung'],
            $data['pendidikan'],
            $data['instagram'],
            $data['linkedin'],
            $data['email'],
            $id
        ]);
    }
    
    // DELETE - Hapus guru
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM guru WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // COUNT - Jumlah guru per kategori
    public function countByJabatan() {
        $stmt = $this->pdo->query("SELECT jabatan, COUNT(*) as total FROM guru GROUP BY jabatan");
        return $stmt->fetchAll();
    }
}
?>