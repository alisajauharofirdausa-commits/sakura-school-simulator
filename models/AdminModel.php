<?php
/**
 * AdminModel
 * Mengelola akses data ke tabel ADMIN (dipakai untuk autentikasi login).
 */
class AdminModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByNomorId(string $nomorId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ADMIN WHERE nomor_id = ?");
        $stmt->execute([$nomorId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function verifyLogin(string $nomorId, string $password): ?array
    {
        $admin = $this->findByNomorId($nomorId);
        if (!$admin) {
            return null;
        }
        if (password_verify($password, $admin['password'])) {
            return $admin;
        }
        return null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO ADMIN (nomor_id, nama, kontak, email, password) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['nomor_id'],
            $data['nama'],
            $data['kontak'] ?? null,
            $data['email'] ?? null,
            password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
    }

    public function all(): array
    {
        return $this->pdo->query("SELECT nomor_id, nama, kontak, email FROM ADMIN ORDER BY nama ASC")->fetchAll();
    }
}
