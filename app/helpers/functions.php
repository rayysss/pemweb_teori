<?php
/**
 * Kumpulan fungsi helper global untuk ZORA Electronics.
 * Berisi utilitas URL, sanitasi output (Anti-XSS), keamanan form (CSRF),
 * formatting mata uang, flash message, dan sistem rendering template.
 */

/**
 * Menghasilkan URL absolut berbasis root folder aplikasi.
 * Menjamin link tetap bekerja meskipun folder proyek dipindah-pindah di XAMPP.
 */
function base_url(string $path = ''): string
{
    // Mengambil direktori script saat ini dan menormalisasi backslash Windows.
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    if ($base === '.' || $base === '/') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * Menghasilkan URL langsung ke file aset statis (CSS, JS, gambar).
 */
function asset(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Mengarahkan (redirect) pengguna ke URL internal tertentu lalu menghentikan eksekusi script.
 */
function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

/**
 * Sanitasi data string untuk mencegah serangan Cross-Site Scripting (XSS).
 * Mengubah karakter khusus seperti <, >, ", ' menjadi entitas HTML aman.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Format angka ke standar penulisan mata uang Rupiah Indonesia (Rp 1.000.000).
 */
function rupiah(mixed $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

/**
 * Mengubah string teks menjadi slug URL yang ramah SEO (contoh: "AirBook 14" -> "airbook-14").
 */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'produk';
}

/**
 * Menyimpan atau mengambil pesan kilat (flash message) berbasis sesi.
 * Pesan langsung dihapus dari sesi setelah dibaca sekali (one-time message).
 */
function flash(string $key, mixed $value = null): mixed
{
    // Jika ada 2 argumen: simpan pesan ke sesi.
    if (func_num_args() > 1) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    // Jika hanya 1 argumen: ambil pesan lalu hapus dari sesi.
    $result = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $result;
}

/**
 * Me-render template view dengan layout terpadu (header + view konten + footer).
 *
 * @param string $view Nama file view relatif terhadap folder app/views/ (tanpa .php).
 * @param array  $data Data yang dikirim dari controller untuk ditampilkan di view.
 */
function render(string $view, array $data = []): void
{
    // Mengubah key array asosiatif menjadi variabel lokal di view ($data['title'] -> $title).
    extract($data, EXTR_SKIP);

    $viewFile = __DIR__ . '/../views/' . $view . '.php';

    if (!is_file($viewFile)) {
        http_response_code(500);
        exit('View tidak ditemukan: ' . e($view));
    }

    // Memuat layout standar pembungkus halaman secara berurutan.
    require __DIR__ . '/../views/layouts/header.php';
    require $viewFile;
    require __DIR__ . '/../views/layouts/footer.php';
}

/**
 * Mengambil atau menghasilkan token CSRF acak berbasis sesi browser.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/**
 * Menghasilkan input hidden HTML berisi token CSRF untuk disisipkan ke dalam form POST.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/**
 * Memverifikasi validitas token CSRF dari request POST (Anti-CSRF Attack).
 * Menghentikan proses jika token tidak cocok atau sesi formulir kedaluwarsa.
 */
function verify_csrf(): void
{
    $sent = (string) ($_POST['_token'] ?? '');

    // Gunakan hash_equals untuk perbandingan string konstan guna mencegah timing attacks.
    if (!$sent || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Sesi formulir kedaluwarsa. Silakan kembali dan muat ulang halaman.');
    }
}

/**
 * Mengambil kembali nilai input form sebelumnya saat terjadi kesalahan validasi.
 */
function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

/**
 * Mengembalikan URL gambar produk atau gambar placeholder cadangan jika gambar belum tersedia.
 */
function product_image(?string $image): string
{
    return $image ?: 'https://placehold.co/800x900/eaf1f9/174a82?text=ZORA';
}

/**
 * Memeriksa apakah request HTTP saat ini menggunakan metode POST.
 */
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
