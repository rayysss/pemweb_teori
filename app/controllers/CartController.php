<?php

class CartController
{
    /**
     * Menampilkan halaman keranjang.
     */
    public function index(): void
    {
        $items = Cart::items();

        render('cart/index', [
            'pageTitle' => 'Keranjang',
            'items'     => $items,
            'subtotal'  => Cart::subtotal(),
        ]);
    }

    /**
     * Menambahkan produk ke keranjang.
     *
     * UX FIX (H10): Sebelumnya selalu redirect ke halaman daftar produk,
     * sehingga user yang menambah produk dari halaman detail kehilangan konteks.
     *
     * FIX: Redirect kembali ke HTTP_REFERER jika referrer berasal dari
     * domain yang sama dan path valid. Fallback ke halaman produk.
     */
    public function add(): void
    {
        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

        try {
            if ($productId <= 0) {
                throw new InvalidArgumentException('Produk tidak valid.');
            }

            Cart::add($productId, $quantity);

            flash('success', 'Produk berhasil ditambahkan ke keranjang.');

        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        // Redirect kembali ke halaman asal jika referrer valid.
        $redirectTo = $this->safeReferer('products');
        redirect($redirectTo);
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function update(): void
    {
        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $itemId   = (int) ($_POST['item_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        try {
            if ($itemId <= 0) {
                throw new InvalidArgumentException('Item keranjang tidak valid.');
            }

            Cart::update($itemId, $quantity);

            flash('success', 'Keranjang berhasil diperbarui.');

        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('cart');
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(): void
    {
        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $itemId = (int) ($_POST['item_id'] ?? 0);

        try {
            if ($itemId <= 0) {
                throw new InvalidArgumentException('Item keranjang tidak valid.');
            }

            Cart::remove($itemId);

            flash('success', 'Produk berhasil dihapus dari keranjang.');

        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect('cart');
    }

    /**
     * Mengembalikan path relatif dari HTTP_REFERER jika aman,
     * atau path fallback jika referrer tidak valid / dari domain lain.
     *
     * @param string $fallback Path fallback (relatif terhadap base URL).
     * @return string
     */
    private function safeReferer(string $fallback): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        if ($referer === '') {
            return $fallback;
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';

        // Hanya ikuti referrer jika dari host yang sama.
        if ($refererHost !== $currentHost) {
            return $fallback;
        }

        // Ambil path relatif dari referrer (tanpa domain).
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $refererPath = parse_url($referer, PHP_URL_PATH) ?? '';

        // Hapus base path agar menjadi path relatif ZORA.
        if ($scriptDir !== '' && str_starts_with($refererPath, $scriptDir)) {
            $relative = ltrim(substr($refererPath, strlen($scriptDir)), '/');
            return $relative !== '' ? $relative : $fallback;
        }

        return $fallback;
    }
}
