<?php
/**
 * Controller Checkout: Menangani proses verifikasi akhir keranjang belanja,
 * pengisian data pengiriman, pemilihan metode bayar, dan penerbitan pesanan.
 */
class CheckoutController
{
    /**
     * Menampilkan halaman formulir checkout.
     * Mewajibkan login dan memastikan keranjang belanja tidak kosong.
     */
    public function index(): void
    {
        // Hanya pengguna yang sudah terdaftar dan login yang boleh checkout.
        require_login();

        $items = Cart::items();

        // Jika keranjang kosong, kembalikan pengunjung ke halaman katalog produk.
        if (!$items) {
            flash('error', 'Keranjang masih kosong.');
            redirect('products');
        }

        $subtotal = Cart::subtotal();

        // Aturan ongkir: Gratis ongkir jika belanja minimal Rp 500.000, selain itu Rp 15.000.
        $shippingCost = $subtotal >= 500000 ? 0 : 15000;

        render('checkout/index', [
            'pageTitle'    => 'Checkout',
            'items'        => $items,
            'subtotal'     => $subtotal,
            'shippingCost' => $shippingCost,
        ]);
    }

    /**
     * Memproses pesanan dari formulir checkout (Metode POST).
     * Melakukan validasi alamat, metode bayar, pengurangan stok, dan pencatatan transaksi.
     */
    public function store(): void
    {
        require_login();

        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $errors = [];

        // Kumpulkan data pengiriman dari form input.
        $shipping = [
            'recipient_name'  => $this->postString('recipient_name'),
            'recipient_phone' => $this->postString('recipient_phone'),
            'address'         => $this->postString('address'),
            'city'            => $this->postString('city'),
            'province'        => $this->postString('province'),
            'postal_code'     => $this->postString('postal_code'),
            'note'            => $this->postString('note'),
        ];

        // Validasi kolom penerima wajib.
        $requiredFields = ['recipient_name', 'recipient_phone', 'address', 'city'];
        foreach ($requiredFields as $field) {
            if (!required_value($shipping[$field])) {
                $errors[] = 'Nama penerima, nomor telepon, alamat, dan kota wajib diisi.';
                break;
            }
        }

        // Validasi format nomor telepon Indonesia.
        if ($shipping['recipient_phone'] !== '' && !preg_match('/^[0-9+\-\s]{8,20}$/', $shipping['recipient_phone'])) {
            $errors[] = 'Nomor telepon tidak valid.';
        }

        // Whitelist opsi metode pembayaran yang didukung sistem.
        $paymentMethod = $this->postString('payment_method', 'bank_transfer');
        $allowedPaymentMethods = ['bank_transfer', 'qris', 'ewallet', 'cod'];
        if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
            $errors[] = 'Metode pembayaran tidak valid.';
        }

        $items    = Cart::items();
        $subtotal = Cart::subtotal();
        $shippingCost = $subtotal >= 500000 ? 0 : 15000;

        if (!$items) {
            flash('error', 'Keranjang masih kosong.');
            redirect('products');
        }

        // Jika terjadi kesalahan input, kembalikan ke form checkout dengan pesan error.
        if ($errors) {
            render('checkout/index', [
                'pageTitle'    => 'Checkout',
                'items'        => $items,
                'subtotal'     => $subtotal,
                'shippingCost' => $shippingCost,
                'errors'       => array_unique($errors),
            ]);
            return;
        }

        try {
            $userId = (int) current_user()['id'];

            // Eksekusi transaksi database: insert pesanan, kunci & kurangi stok, dan kosongkan keranjang.
            $orderId = Order::createFromCart($userId, $shipping, $paymentMethod);

            $order = Order::findForUser($orderId, $userId);
            if (!$order) {
                throw new RuntimeException('Pesanan tidak ditemukan setelah dibuat.');
            }

            // Tampilkan halaman konfirmasi pesanan berhasil.
            render('checkout/success', [
                'pageTitle' => 'Pesanan Berhasil',
                'order'     => $order,
            ]);

        } catch (RuntimeException $e) {
            // Error bisnis (misal stok habis mendadak).
            flash('error', $e->getMessage());
            redirect('cart');

        } catch (Throwable $e) {
            // Error teknis server yang tidak terduga.
            error_log($e->getMessage());
            flash('error', 'Terjadi kesalahan ketika memproses pesanan.');
            redirect('cart');
        }
    }

    /**
     * Membaca dan menormalisasi input data POST bertipe string.
     */
    private function postString(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }
}
