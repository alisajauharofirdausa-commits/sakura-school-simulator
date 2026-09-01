<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/GudangModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$gudangModel = new GudangModel($pdo);

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
        $gudangModel->create($data);
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
        echo json_encode($gudangModel->all());
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 3. UPDATE GUDANG (PUT)
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id_gudang'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "ID gudang wajib dicantumkan!"]);
        exit();
    }

    try {
        $gudangModel->update((int) $id, $data);
        echo json_encode(["message" => "Gudang berhasil diperbarui!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// 4. HAPUS GUDANG (DELETE)
elseif ($method === 'DELETE') {
    $id = $_GET['id_gudang'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "ID gudang wajib dicantumkan!"]);
        exit();
    }

    try {
        $gudangModel->delete((int) $id);
        echo json_encode(["message" => "Gudang berhasil dihapus!"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
