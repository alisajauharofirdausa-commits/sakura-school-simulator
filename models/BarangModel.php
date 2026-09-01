<?php
/**
 * BarangModel
 * Mengelola akses data ke tabel inventory (barang), termasuk join ke
 * storage_unit (gudang) dan vendor_supplier (vendor).
 */
class BarangModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ambil semua barang beserta info gudang & vendor.
     * Bisa difilter dengan kata kunci pencarian (nama_barang / jenis_barang / serial_number).
     */
    public function all(?string $search = null): array
    {
        $sql = "SELECT i.serial_number, i.nama_barang, i.jenis_barang, i.kuantitas_stok, i.harga,
                       i.id_gudang, g.nama_gudang, g.lokasi AS lokasi_gudang,
                       i.id_vendor, v.nama AS nama_vendor, v.kontak AS kontak_vendor
                FROM inventory i
                LEFT JOIN storage_unit g ON i.id_gudang = g.id_gudang
                LEFT JOIN vendor_supplier v ON i.id_vendor = v.id_vendor";

        $params = [];
        if ($search) {
            $sql .= " WHERE i.nama_barang LIKE ? OR i.jenis_barang LIKE ? OR i.serial_number LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= " ORDER BY i.nama_barang ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Tambahkan flag alert stok habis di setiap baris.
        return array_map(function ($item) {
            $stok = (int) $item['kuantitas_stok'];
            $item['stok_habis_alert'] = ($stok <= 0);
            $item['pemberitahuan'] = ($stok <= 0) ? 'ALERT: Stok Barang Habis!' : 'Stok Tersedia';
            return $item;
        }, $rows);
    }

    public function find(string $serialNumber): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM inventory WHERE serial_number = ?");
        $stmt->execute([$serialNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO inventory (serial_number, nama_barang, jenis_barang, kuantitas_stok, harga, id_gudang, id_vendor)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['serial_number'],
            $data['nama_barang'],
            $data['jenis_barang'] ?? null,
            $data['kuantitas_stok'] ?? 0,
            $data['harga'] ?? 0,
            $data['id_gudang'] ?? null,
            $data['id_vendor'] ?? null,
        ]);
    }

    public function update(string $serialNumber, array $data): bool
    {
        $sql = "UPDATE inventory
                SET nama_barang = ?, jenis_barang = ?, kuantitas_stok = ?, harga = ?, id_gudang = ?, id_vendor = ?
                WHERE serial_number = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama_barang'],
            $data['jenis_barang'] ?? null,
            $data['kuantitas_stok'] ?? 0,
            $data['harga'] ?? 0,
            $data['id_gudang'] ?? null,
            $data['id_vendor'] ?? null,
            $serialNumber,
        ]);
    }

    public function delete(string $serialNumber): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM inventory WHERE serial_number = ?");
        return $stmt->execute([$serialNumber]);
    }
}
