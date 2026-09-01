<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AdminModel.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$adminModel = new AdminModel($pdo);

// 1. PROSES LOGIN ADMIN (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $nomor_id = $data['nomor_id'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($nomor_id) || empty($password)) {
        http_response_code(400);
        echo json_encode(["message" => "Nomor ID dan password wajib diisi!"]);
        exit();
    }

    try {
        $admin = $adminModel->verifyLogin($nomor_id, $password);

        if ($admin) {
            $_SESSION['admin'] = [
                'nomor_id' => $admin['nomor_id'],
                'nama'     => $admin['nama'],
            ];

            echo json_encode([
                "success" => true,
                "message" => "Login berhasil!",
                "user" => $_SESSION['admin']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Nomor ID atau password salah!"]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => $e->getMessage()]);
    }
}

// 2. CEK STATUS LOGIN ADMIN (GET)
elseif ($method === 'GET') {
    if (isset($_SESSION['admin'])) {
        echo json_encode(["logged_in" => true, "user" => $_SESSION['admin']]);
    } else {
        echo json_encode(["logged_in" => false]);
    }
}

// 3. LOGOUT (DELETE)
elseif ($method === 'DELETE') {
    $_SESSION = [];
    session_destroy();
    echo json_encode(["message" => "Logout berhasil!"]);
}
