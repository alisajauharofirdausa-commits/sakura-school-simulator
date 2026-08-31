<?php
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// 1. TAMBAH VENDOR/SUPPLIER BARU (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $nama = $data['nama'] ?? '';
    $kontak = $data['kontak'] ?? '';

    if (empty($nama)) {
        http_response_code(400);
        echo json_encode(["message" => "Nama vendor wajib diisi!"]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO vendor_supplier (nama, kontak) VALUES (?, ?)");
        $stmt->execute([$nama, $kontak]);
        
        http_response_code(201);
        echo json_encode(["message" => "Vendor/Supplier baru berhasil ditambahkan!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} 
// 2. GET DAFTAR VENDOR (Untuk Pilihan Dropdown di Frontend)
elseif ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM vendor_supplier ORDER BY nama ASC");
        echo json_encode($stmt->fetchAll());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>