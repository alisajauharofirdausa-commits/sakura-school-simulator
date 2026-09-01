<?php
/**
 * Jalankan file ini SEKALI lewat browser untuk membuat akun admin pertama,
 * lalu HAPUS file ini setelah dipakai.
 *
 * Akses: http://localhost/pengelolahan_inventory/seed_admin.php
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/AdminModel.php';

header('Content-Type: text/html; charset=UTF-8');

$adminModel = new AdminModel($pdo);

$nomor_id = 'ADM001';
$nama     = 'Admin Utama';
$kontak   = '081211112222';
$email    = 'admin@contoh.com';
$password = 'admin123'; // Ganti setelah login pertama kali!

if ($adminModel->findByNomorId($nomor_id)) {
    die("Admin dengan nomor_id '{$nomor_id}' sudah ada. Seeder dibatalkan.");
}

$adminModel->create([
    'nomor_id' => $nomor_id,
    'nama'     => $nama,
    'kontak'   => $kontak,
    'email'    => $email,
    'password' => $password,
]);

echo "Admin berhasil dibuat!<br>";
echo "Nomor ID: {$nomor_id}<br>";
echo "Password: {$password}<br>";
echo "<strong>Silakan hapus file seed_admin.php ini sekarang.</strong>";
