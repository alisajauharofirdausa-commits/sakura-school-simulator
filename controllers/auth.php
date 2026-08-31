<?php
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

// 1. PROSES LOGIN ADMIN (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["message" => "Username dan password wajib diisi!"]);
        exit();
    }

    try {
        // Query langsung ke tabel admin
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && ($password === $admin['password'] || password_verify($password, $admin['password']))) {
            $_SESSION['admin'] = [
                'id_admin' => $admin['id_admin'],
                'username' => $admin['username'],
                'nama_lengkap' => $admin['nama_lengkap']
            ];

            echo json_encode([
                "success" => true,
                "message" => "Login berhasil!",
                "user" => $_SESSION['admin']
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Username atau password salah!"]);
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
    session_destroy();
    echo json_encode(["message" => "Logout berhasil!"]);
}
?>