<?php
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. TAMBAH GUDANG CABANG BARU (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $nama_gudang = $data['nama_gudang'] ?? '';
    $lokasi = $data['lokasi'] ?? '';

    if (empty($nama_gudang) || empty($lokasi)) {
        http_response_code(400);
        echo json_encode(["message" => "Nama gudang dan lokasi wajib diisi!"]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO storage_unit (nama_gudang, lokasi) VALUES (?, ?)");
        $stmt->execute([$nama_gudang, $lokasi]);
        
        http_response_code(201);
        echo json_encode(["message" => "Gudang cabang baru berhasil ditambahkan!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} 
// 2. GET DAFTAR GUDANG (Untuk Pilihan Dropdown di Frontend)
elseif ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM storage_unit ORDER BY nama_gudang ASC");
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>