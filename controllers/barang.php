<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/BarangModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$barangModel = new BarangModel($pdo);

// 1. TAMBAH BARANG BARU (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $barangModel->create($data);
        http_response_code(201);
        echo json_encode(["message" => "Barang berhasil dimasukkan ke inventory!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 2. MEMANTAU BARANG & FITUR PENCARIAN (GET)
elseif ($method === 'GET') {
    $search = $_GET['search'] ?? ($_GET['q'] ?? null);

    try {
        echo json_encode($barangModel->all($search));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 3. UPDATE STOK / BARANG (PUT)
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $serial_number = $data['serial_number'] ?? null;

    if (!$serial_number) {
        http_response_code(400);
        echo json_encode(["message" => "Serial number wajib dicantumkan!"]);
        exit();
    }

    try {
        $barangModel->update($serial_number, $data);
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
        $barangModel->delete($serial_number);
        echo json_encode(["message" => "Barang berhasil dihapus!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
