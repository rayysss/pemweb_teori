<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/validation.php';

spl_autoload_register(function (string $class): void {
    foreach ([
        __DIR__ . '/../app/models/' . $class . '.php',
        __DIR__ . '/../app/controllers/' . $class . '.php',
    ] as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// Periksa koneksi database saat startup.
try {
    db();
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo '<h1>Koneksi database ZORA gagal</h1>'
        . '<p>Pastikan MySQL aktif, database <strong>zora</strong> sudah diimpor, '
        . 'dan konfigurasi pada <code>config/database.php</code> benar.</p>';
    exit;
}

// Parse URL path.
$path   = trim((string) ($_GET['url'] ?? ''), '/');
$parts  = $path === '' ? [] : explode('/', $path);
$first  = $parts[0] ?? 'home';
$second = $parts[1] ?? '';
$third  = $parts[2] ?? '';
$fourth = $parts[3] ?? '';

try {
    switch ($first) {

        // ── Halaman Utama ─────────────────────────────────────────────────────
        case '':
        case 'home':
            (new HomeController())->index();
            break;

        // ── Produk ───────────────────────────────────────────────────────────
        case 'products':
            $controller = new ProductController();
            if ($second !== '') {
                $controller->show($second);
            } else {
                $controller->index();
            }
            break;

        // ── Autentikasi ───────────────────────────────────────────────────────
        case 'login':
            (new AuthController())->login();
            break;

        case 'register':
            (new AuthController())->register();
            break;

        case 'logout':
            (new AuthController())->logout();
            break;

        // ── Keranjang ─────────────────────────────────────────────────────────
        case 'cart':
            $cart = new CartController();
            match ($second) {
                'add'    => $cart->add(),
                'update' => $cart->update(),
                'remove' => $cart->remove(),
                default  => $cart->index(),
            };
            break;

        // ── Checkout ──────────────────────────────────────────────────────────
        case 'checkout':
            $checkout = new CheckoutController();
            if (is_post()) {
                $checkout->store();
            } else {
                $checkout->index();
            }
            break;

        // ── Akun Pelanggan ────────────────────────────────────────────────────
        case 'account':
            require_login();
            render('account/index', ['pageTitle' => 'Akun Saya']);
            break;

        case 'orders':
            $orders = new OrderController();
            if ($second !== '') {
                $orders->show($second);
            } else {
                $orders->index();
            }
            break;

        // ── Admin ─────────────────────────────────────────────────────────────
        case 'admin':
            $admin = new AdminController();

            if ($second === 'products') {
                // FIX (H6): Tambahkan route untuk edit dan update produk yang
                // sebelumnya sudah dikodekan di controller tapi tidak punya route.
                match ($third) {
                    'create'  => $admin->createProduct(),
                    'delete'  => $admin->deleteProduct(),
                    'restore' => $admin->restoreProduct(),  // POST: batalkan arsip / aktifkan produk
                    'update'  => $admin->updateProduct(),   // POST: simpan perubahan
                    default   => (function () use ($admin, $third, $fourth): void {
                        // /admin/products/edit/{id}
                        if ($third === 'edit') {
                            // Ambil ID dari segmen keempat URL: /admin/products/edit/5
                            $productId = (int) $fourth;
                            $admin->editProduct($productId);
                        } else {
                            $admin->products();
                        }
                    })(),
                };
            } elseif ($second === 'orders' && $third === 'update') {
                $admin->updateOrderStatus();
            } else {
                $admin->index();
            }
            break;

        // ── 404 ───────────────────────────────────────────────────────────────
        default:
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Halaman tidak ditemukan']);
    }

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo '<main style="font-family:Arial;padding:40px">'
        . '<h1>Terjadi kesalahan</h1>'
        . '<p>Permintaan belum dapat diproses. Periksa konfigurasi dan log PHP.</p>'
        . '</main>';
}
