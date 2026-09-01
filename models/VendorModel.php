<?php
/**
 * VendorModel
 * Mengelola akses data ke tabel vendor_supplier.
 */
class VendorModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        return $this->pdo->query("SELECT * FROM vendor_supplier ORDER BY nama ASC")->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM vendor_supplier WHERE id_vendor = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO vendor_supplier (nama, kontak) VALUES (?, ?)");
        return $stmt->execute([$data['nama'], $data['kontak'] ?? null]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("UPDATE vendor_supplier SET nama = ?, kontak = ? WHERE id_vendor = ?");
        return $stmt->execute([$data['nama'], $data['kontak'] ?? null, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM vendor_supplier WHERE id_vendor = ?");
        return $stmt->execute([$id]);
    }
}
