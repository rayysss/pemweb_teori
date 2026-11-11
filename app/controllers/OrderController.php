<?php
/**
 * Controller Pesanan (Order): Menangani riwayat transaksi belanja pelanggan
 * dan rincian detail pesanan individual.
 */
class OrderController
{
    /**
     * Menampilkan daftar seluruh riwayat pesanan milik pelanggan yang sedang login.
     */
    public function index(): void
    {
        require_login();

        $userId = (int) current_user()['id'];

        // Ambil riwayat pesanan khusus untuk ID user aktif (Mencegah IDOR).
        $orders = Order::forUser($userId);

        render('account/orders', [
            'pageTitle' => 'Pesanan Saya',
            'orders'    => $orders,
        ]);
    }

    /**
     * Menampilkan rincian lengkap satu pesanan berdasarkan ID pesanan.
     * Dilengkapi verifikasi kepemilikan untuk mencegah akses tidak sah ke pesanan orang lain (Anti-IDOR).
     */
    public function show(string $id): void
    {
        require_login();

        $orderId = filter_var($id, FILTER_VALIDATE_INT);

        // Jika ID pesanan bukan integer valid atau <= 0, kirim response 404.
        if ($orderId === false || $orderId <= 0) {
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Pesanan tidak ditemukan']);
            return;
        }

        $userId = (int) current_user()['id'];

        // Proteksi IDOR: Hanya ambil data pesanan jika user_id pada pesanan cocok dengan user yang sedang login.
        $order = Order::findForUser($orderId, $userId);

        if (!$order) {
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Pesanan tidak ditemukan']);
            return;
        }

        // Ambil snapshot rincian barang dan status pembayaran pesanan.
        $items   = Order::items($orderId);
        $payment = Payment::forOrder($orderId);

        render('account/order', [
            'pageTitle' => 'Detail Pesanan',
            'order'     => $order,
            'items'     => $items,
            'payment'   => $payment,
        ]);
    }
}
