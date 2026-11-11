<?php
/**
 * Controller Autentikasi: Menangani alur masuk (login), pendaftaran akun baru (register),
 * penggabungan keranjang belanja tamu ke keranjang akun, serta keluar sistem (logout).
 */
class AuthController
{
    /**
     * Menampilkan form login dan memproses autentikasi pengguna.
     */
    public function login(): void
    {
        // Jika sudah login, langsung alihkan ke panel yang sesuai hak aksesnya.
        if (is_logged_in()) {
            redirect(is_admin() ? 'admin' : 'account');
        }

        $errors = [];

        if (is_post()) {
            // Verifikasi token CSRF sebelum memproses data sensitif.
            verify_csrf();

            $email    = is_string($_POST['email'] ?? null) ? strtolower(trim($_POST['email'])) : '';
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

            // Validasi format email.
            if (!valid_email($email)) {
                $errors[] = 'Format email tidak valid.';
            } else {
                // Cocokkan email dan password ke database.
                $user = User::authenticate($email, $password);

                if ($user) {
                    $oldSessionId = session_id();

                    // Simpan data profil pengguna ke dalam sesi login.
                    $_SESSION['user'] = $user;

                    // Regenerasi Session ID untuk mencegah serangan Session Fixation.
                    session_regenerate_id(true);

                    // Gabungkan item yang ada di keranjang belanja tamu ke akun member.
                    Cart::mergeGuestCart((int) $user['id'], $oldSessionId);

                    redirect(is_admin() ? 'admin' : 'account');
                }

                // Berikan pesan ambigu demi keamanan (tidak membocorkan apakah email atau password yang salah).
                $errors[] = 'Email atau password tidak sesuai.';
            }
        }

        render('auth/login', [
            'pageTitle' => 'Masuk',
            'errors'    => $errors,
        ]);
    }

    /**
     * Menampilkan form pendaftaran akun baru dan menyimpan data pelanggan.
     */
    public function register(): void
    {
        if (is_logged_in()) {
            redirect(is_admin() ? 'admin' : 'account');
        }

        $errors = [];

        if (is_post()) {
            verify_csrf();

            $name     = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
            $email    = is_string($_POST['email'] ?? null) ? strtolower(trim($_POST['email'])) : '';
            $phone    = is_string($_POST['phone'] ?? null) ? trim($_POST['phone']) : '';
            $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
            $confirm  = is_string($_POST['password_confirmation'] ?? null) ? $_POST['password_confirmation'] : '';

            // Validasi kelengkapan data.
            if (!required_value($name)) {
                $errors[] = 'Nama wajib diisi.';
            }

            if (!valid_email($email)) {
                $errors[] = 'Format email tidak valid.';
            } elseif (User::findByEmail($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }

            if (!valid_password($password)) {
                $errors[] = 'Password minimal 8 karakter.';
            }

            if ($password !== $confirm) {
                $errors[] = 'Konfirmasi password tidak sama.';
            }

            // Jika semua validasi lolos, simpan akun baru.
            if (!$errors) {
                try {
                    $oldSessionId = session_id();

                    // Buat user baru di tabel users.
                    $userId = User::create([
                        'name'     => $name,
                        'email'    => $email,
                        'phone'    => $phone,
                        'password' => $password,
                    ]);

                    $user = User::find($userId);
                    if (!$user) {
                        throw new RuntimeException('Data pengguna gagal dibuat.');
                    }

                    // Otomatis login setelah registrasi berhasil.
                    $_SESSION['user'] = $user;
                    session_regenerate_id(true);

                    // Migrasikan keranjang belanja sesi tamu ke akun baru.
                    Cart::mergeGuestCart($userId, $oldSessionId);

                    redirect('account');

                } catch (Throwable $e) {
                    error_log($e->getMessage());
                    $errors[] = 'Registrasi gagal. Periksa email dan coba kembali.';
                }
            }
        }

        render('auth/register', [
            'pageTitle' => 'Buat Akun',
            'errors'    => $errors,
        ]);
    }

    /**
     * Mengakhiri sesi pengguna (Logout).
     * Wajib menggunakan metode POST dan menyertakan token CSRF untuk mencegah Logout CSRF.
     */
    public function logout(): void
    {
        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        // Hapus data pengguna dari session.
        unset($_SESSION['user']);

        // Ganti ID sesi lama dengan ID baru yang bersih.
        session_regenerate_id(true);

        redirect('home');
    }
}
