<?php
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. TAMBAH BARANG BARU (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $sql = "INSERT INTO inventory (serial_number, nama_barang, jenis_barang, kuantitas_stok, harga, id_gudang, id_vendor) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['serial_number'],
            $data['nama_barang'],
            $data['jenis_barang'] ?? null,
            $data['kuantitas_stok'] ?? 0,
            $data['harga'] ?? 0,
            $data['id_gudang'] ?? null,
            $data['id_vendor'] ?? null
        ]);
        
        http_response_code(201);
        echo json_encode(["message" => "Barang berhasil dimasukkan ke inventory!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} 

// 2. MEMANTAU BARANG & FITUR PENCARIAN (GET)
elseif ($method === 'GET') {
    $search = $_GET['search'] ?? null;

    try {
        // Query Relasi (Memantau stok, lokasi gudang, harga, dan vendor)
        $sql = "SELECT i.serial_number, i.nama_barang, i.jenis_barang, i.kuantitas_stok, i.harga,
                       i.id_gudang, g.nama_gudang, g.lokasi AS lokasi_gudang,
                       i.id_vendor, v.nama AS nama_vendor, v.kontak AS kontak_vendor
                FROM inventory i
                LEFT JOIN storage_unit g ON i.id_gudang = g.id_gudang
                LEFT JOIN vendor_supplier v ON i.id_vendor = v.id_vendor";

        $params = [];
        // Fitur Kolom Pencarian
        if ($search) {
            $sql .= " WHERE i.nama_barang LIKE ? OR i.jenis_barang LIKE ? OR i.serial_number LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }

        $sql .= " ORDER BY i.nama_barang ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Fitur Alert Stok Habis (Sistem menunjukkan alert ke admin jika kuantitas_stok <= 0)
        $result = array_map(function($item) {
            $stok = (int)$item['kuantitas_stok'];
            $item['stok_habis_alert'] = ($stok <= 0);
            $item['pemberitahuan'] = ($stok <= 0) ? "ALERT: Stok Barang Habis!" : "Stok Tersedia";
            return $item;
        }, $rows);

        echo json_encode($result);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 3. UPDATE STOK / BARANG (PUT)
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $sql = "UPDATE inventory 
                SET nama_barang = ?, jenis_barang = ?, kuantitas_stok = ?, harga = ?, id_gudang = ?, id_vendor = ?
                WHERE serial_number = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['nama_barang'],
            $data['jenis_barang'],
            $data['kuantitas_stok'],
            $data['harga'],
            $data['id_gudang'],
            $data['id_vendor'],
            $data['serial_number']
        ]);

        echo json_encode(["message" => "Data barang berhasil diperbarui!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 4. HAPUS BARANG (DELETE)
elseif ($method === 'DELETE') {
    $serial_number = $_GET['serial_number'] ?? null;

    if (!$serial_number) {
        http_response_code(400);
        echo json_encode(["message" => "Serial number wajib dicantumkan!"]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE serial_number = ?");
        $stmt->execute([$serial_number]);

        echo json_encode(["message" => "Barang berhasil dihapus!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>