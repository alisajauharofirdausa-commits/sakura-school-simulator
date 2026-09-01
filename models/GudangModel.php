<?php
/**
 * GudangModel
 * Mengelola akses data ke tabel storage_unit.
 */
class GudangModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        return $this->pdo->query("SELECT * FROM storage_unit ORDER BY nama_gudang ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM storage_unit WHERE id_gudang = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO storage_unit (nama_gudang, lokasi) VALUES (?, ?)");
        return $stmt->execute([$data['nama_gudang'], $data['lokasi']]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("UPDATE storage_unit SET nama_gudang = ?, lokasi = ? WHERE id_gudang = ?");
        return $stmt->execute([$data['nama_gudang'], $data['lokasi'], $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM storage_unit WHERE id_gudang = ?");
        return $stmt->execute([$id]);
    }
}
