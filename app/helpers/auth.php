<?php
/**
 * Helper autentikasi dan otorisasi pengguna (Authentication & Access Control).
 * Berfungsi sebagai middleware / guard sederhana untuk melindungi halaman sensitif.
 */

/**
 * Mengambil data pengguna yang sedang login saat ini dari sesi browser.
 * Mengembalikan array asosiatif profil pengguna atau null jika belum login.
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Memeriksa apakah pengunjung saat ini sudah berstatus login.
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['user']['id']);
}

/**
 * Memeriksa apakah pengguna yang sedang login memiliki hak akses peran 'admin'.
 */
function is_admin(): bool
{
    return is_logged_in() && (($_SESSION['user']['role'] ?? '') === 'admin');
}

/**
 * Proteksi rute (Auth Guard): Mewajibkan pengguna untuk login terlebih dahulu.
 * Jika belum login, redirect ke halaman login dengan pesan peringatan.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('login');
    }
}

/**
 * Proteksi rute (Admin Guard): Mewajibkan pengguna memiliki peran 'admin'.
 * Menghentikan request dengan kode HTTP 403 jika pengguna bukan admin.
 */
function require_admin(): void
{
    // Pastikan sudah login terlebih dahulu.
    require_login();

    // Jika sudah login tapi bukan admin, tolak akses.
    if (!is_admin()) {
        http_response_code(403);
        render('home/not-found', ['pageTitle' => 'Akses ditolak']);
        exit;
    }
}
