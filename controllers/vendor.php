<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/VendorModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$vendorModel = new VendorModel($pdo);

// 1. TAMBAH VENDOR/SUPPLIER BARU (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nama = $data['nama'] ?? '';

    if (empty($nama)) {
        http_response_code(400);
        echo json_encode(["message" => "Nama vendor wajib diisi!"]);
        exit();
    }

    try {
        $vendorModel->create($data);
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
        echo json_encode($vendorModel->all());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 3. UPDATE VENDOR (PUT)
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id_vendor'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "ID vendor wajib dicantumkan!"]);
        exit();
    }

    try {
        $vendorModel->update((int) $id, $data);
        echo json_encode(["message" => "Vendor berhasil diperbarui!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 4. HAPUS VENDOR (DELETE)
elseif ($method === 'DELETE') {
    $id = $_GET['id_vendor'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "ID vendor wajib dicantumkan!"]);
        exit();
    }

    try {
        $vendorModel->delete((int) $id);
        echo json_encode(["message" => "Vendor berhasil dihapus!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
