<?php
/**
 * Model User: Mengelola data pengguna (pelanggan dan administrator).
 * Menangani pencarian profil, registrasi akun baru, dan proses autentikasi (login).
 */
class User
{
    /**
     * Mencari data pengguna berdasarkan alamat email (case-insensitive).
     */
    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower(trim($email))]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Mengambil data profil publik pengguna berdasarkan ID (tanpa menyertakan password hash).
     */
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare(
            'SELECT id, name, email, phone, role, status, created_at
             FROM users
             WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Mendaftarkan pengguna baru dengan peran default 'customer'.
     * Password selalu dienkripsi satu arah menggunakan bcrypt (password_hash).
     */
    public static function create(array $data): int
    {
        $stmt = db()->prepare(
            "INSERT INTO users (name, email, phone, password, role, status)
             VALUES (?, ?, ?, ?, 'customer', 'active')"
        );

        $stmt->execute([
            $data['name'],
            strtolower($data['email']),
            $data['phone'] ?: null,
            // Hashing password menggunakan algoritma default yang aman (BCrypt/Argon2).
            password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        return (int) db()->lastInsertId();
    }

    /**
     * Memverifikasi kredensial login (email dan password).
     * Jika valid dan akun aktif, perbarui last_login_at dan kembalikan data user tanpa hash password.
     */
    public static function authenticate(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);

        // Periksa keberadaan user, status akun, dan validitas password via password_verify.
        if (!$user || $user['status'] !== 'active' || !password_verify($password, $user['password'])) {
            return null;
        }

        // Catat waktu login terakhir untuk audit aktivitas.
        db()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);

        // Hapus hash password sebelum disimpan ke dalam session browser demi keamanan.
        unset($user['password']);

        return $user;
    }

    /**
     * Memperbarui informasi nama dan nomor telepon pengguna.
     */
    public static function updateProfile(int $id, string $name, string $phone): void
    {
        $stmt = db()->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
        $stmt->execute([$name, $phone ?: null, $id]);
    }
}
