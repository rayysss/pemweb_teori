<?php

class AdminController
{
    /**
     * Dashboard administrator: statistik toko + pesanan terbaru.
     */
    public function index(): void
    {
        require_admin();

        $pdo   = db();
        $stats = [
            'products'  => (int) $pdo->query(
                "SELECT COUNT(*) FROM products WHERE status = 'active'"
            )->fetchColumn(),

            'orders'    => (int) $pdo->query(
                "SELECT COUNT(*) FROM orders"
            )->fetchColumn(),

            'customers' => (int) $pdo->query(
                "SELECT COUNT(*) FROM users WHERE role = 'customer'"
            )->fetchColumn(),

            'revenue'   => (float) $pdo->query(
                "SELECT COALESCE(SUM(total_amount), 0)
                 FROM orders
                 WHERE order_status IN ('confirmed','processing','shipped','completed')"
            )->fetchColumn(),
        ];

        render('admin/index', [
            'pageTitle' => 'Dashboard Admin',
            'stats'     => $stats,
            'orders'    => array_slice(Order::all(), 0, 8),
        ]);
    }

    /**
     * Menampilkan daftar produk untuk admin.
     */
    public function products(): void
    {
        require_admin();

        render('admin/products', [
            'pageTitle'  => 'Kelola Produk',
            'products'   => Product::adminAll(),
            'categories' => Category::all(false),
        ]);
    }

    /**
     * Menambahkan produk baru.
     */
    public function createProduct(): void
    {
        require_admin();

        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $categoryInput = $_POST['category_id'] ?? '';
        $categoryId    = $categoryInput === ''
            ? null
            : filter_var($categoryInput, FILTER_VALIDATE_INT);

        $data = [
            'category_id'      => $categoryId,
            'name'             => trim((string) ($_POST['name'] ?? '')),
            'sku'              => trim((string) ($_POST['sku'] ?? '')),
            'description'      => trim((string) ($_POST['description'] ?? '')),
            'price'            => (float) ($_POST['price'] ?? 0),
            'compare_at_price' => trim((string) ($_POST['compare_at_price'] ?? '')),
            'stock'            => max(0, (int) ($_POST['stock'] ?? 0)),
            'color'            => trim((string) ($_POST['color'] ?? '')),
            'tag'              => trim((string) ($_POST['tag'] ?? '')),
            'image_url'        => trim((string) ($_POST['image_url'] ?? '')),
            'is_featured'      => !empty($_POST['is_featured']),
        ];

        $errors = $this->validateProductData($data, $categoryInput, $categoryId);

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('admin/products');
            return; // FIX (M8): tambahkan return setelah redirect agar eksekusi berhenti.
        }

        try {
            Product::create($data);
            flash('success', 'Produk berhasil ditambahkan.');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            flash('error', 'Gagal menyimpan produk. Periksa SKU dan data produk.');
        }

        redirect('admin/products');
    }

    /**
     * Menampilkan formulir edit produk.
     *
     * FIX (H6): Method ini sebelumnya ada tapi tidak memiliki route.
     * Route sudah ditambahkan di public/index.php.
     */
    public function editProduct(int $id): void
    {
        require_admin();

        if ($id <= 0) {
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Produk tidak ditemukan']);
            return;
        }

        $product = Product::find($id);

        if (!$product) {
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Produk tidak ditemukan']);
            return;
        }

        render('admin/product-edit', [
            'pageTitle'  => 'Edit Produk — ' . $product['name'],
            'product'    => $product,
            'categories' => Category::all(false),
        ]);
    }

    /**
     * Menyimpan perubahan produk (dari form edit).
     *
     * FIX (H6): Method ini sebelumnya ada tapi tidak memiliki route.
     * Route sudah ditambahkan di public/index.php.
     */
    public function updateProduct(): void
    {
        require_admin();

        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $id      = (int) ($_POST['product_id'] ?? 0);
        $product = Product::find($id);

        if ($id <= 0 || !$product) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('admin/products');
            return;
        }

        $categoryInput = $_POST['category_id'] ?? '';
        $categoryId    = $categoryInput === ''
            ? null
            : filter_var($categoryInput, FILTER_VALIDATE_INT);

        $data = [
            'category_id'      => $categoryId,
            'name'             => trim((string) ($_POST['name'] ?? '')),
            'sku'              => trim((string) ($_POST['sku'] ?? '')),
            'description'      => trim((string) ($_POST['description'] ?? '')),
            'price'            => (float) ($_POST['price'] ?? 0),
            'compare_at_price' => trim((string) ($_POST['compare_at_price'] ?? '')),
            'stock'            => max(0, (int) ($_POST['stock'] ?? 0)),
            'color'            => trim((string) ($_POST['color'] ?? '')),
            'tag'              => trim((string) ($_POST['tag'] ?? '')),
            'image_url'        => trim((string) ($_POST['image_url'] ?? '')),
            'status'           => $_POST['status'] ?? 'active',
            'is_featured'      => !empty($_POST['is_featured']),
        ];

        $errors = $this->validateProductData($data, $categoryInput, $categoryId, true);

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('admin/products/edit/' . $id);
            return;
        }

        try {
            Product::update($id, $data);
            flash('success', 'Produk berhasil diperbarui.');
            redirect('admin/products');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            flash('error', 'Gagal memperbarui produk. Periksa SKU dan kategori.');
            redirect('admin/products/edit/' . $id);
        }
    }

    /**
     * Mengarsipkan produk (soft-delete).
     */
    public function deleteProduct(): void
    {
        require_admin();

        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $id = (int) ($_POST['product_id'] ?? 0);

        if ($id <= 0 || !Product::find($id)) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('admin/products');
            return;
        }

        try {
            Product::delete($id);
            flash('success', 'Produk berhasil diarsipkan.');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            flash('error', 'Gagal mengarsipkan produk.');
        }

        redirect('admin/products');
    }

    /**
     * Membatalkan arsip produk (mengaktifkan kembali produk ke katalog penjualan).
     */
    public function restoreProduct(): void
    {
        require_admin();

        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $id = (int) ($_POST['product_id'] ?? 0);

        if ($id <= 0 || !Product::find($id)) {
            flash('error', 'Produk tidak ditemukan.');
            redirect('admin/products');
            return;
        }

        try {
            Product::restore($id);
            flash('success', 'Produk berhasil diaktifkan kembali dan kini tampil di katalog.');
        } catch (Throwable $e) {
            error_log($e->getMessage());
            flash('error', 'Gagal mengaktifkan kembali produk.');
        }

        redirect('admin/products');
    }

    /**
     * Memperbarui status pesanan.
     *
     * SECURITY FIX (H7): Sebelumnya $e->getMessage() langsung dikirim ke
     * flash('error') yang ditampilkan ke user — berpotensi information
     * disclosure (nama tabel, query SQL, dsb.).
     *
     * FIX: Exception di-log ke server, user hanya menerima pesan generik
     * kecuali untuk pesan yang sudah disanitasi (InvalidArgumentException
     * dengan pesan yang aman).
     */
    public function updateOrderStatus(): void
    {
        require_admin();

        if (!is_post()) {
            http_response_code(405);
            exit('Method not allowed.');
        }

        verify_csrf();

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status  = (string) ($_POST['status'] ?? '');

        try {
            if ($orderId <= 0) {
                throw new InvalidArgumentException('ID pesanan tidak valid.');
            }

            Order::updateStatus($orderId, $status);

            flash('success', 'Status pesanan berhasil diperbarui.');

        } catch (InvalidArgumentException $e) {
            // Pesan ini aman untuk ditampilkan ke admin.
            flash('error', $e->getMessage());

        } catch (RuntimeException $e) {
            // Pesan runtime dari Order::updateStatus juga aman (sudah disanitasi di model).
            flash('error', $e->getMessage());

        } catch (Throwable $e) {
            // Exception lain: log detail, tampilkan pesan generik.
            error_log('[Admin] updateOrderStatus error: ' . $e->getMessage());
            flash('error', 'Terjadi kesalahan saat memperbarui status pesanan.');
        }

        redirect('admin');
    }

    /**
     * Validasi data produk (digunakan oleh createProduct dan updateProduct).
     *
     * @param array      $data          Data produk yang sudah di-sanitasi.
     * @param string     $categoryInput Input kategori mentah dari POST.
     * @param int|false|null $categoryId ID kategori setelah filter_var.
     * @param bool       $isUpdate      True jika sedang melakukan update (ada field tambahan).
     * @return array                    Daftar pesan error (kosong jika valid).
     */
    private function validateProductData(
        array          $data,
        string         $categoryInput,
        int|false|null $categoryId,
        bool           $isUpdate = false
    ): array {
        $errors = [];

        if (!required_value($data['name'])) {
            $errors[] = 'Nama produk wajib diisi.';
        }

        if (!required_value($data['sku'])) {
            $errors[] = 'SKU wajib diisi.';
        }

        if ($data['price'] <= 0) {
            $errors[] = 'Harga produk harus lebih dari nol.';
        }

        if (
            $data['compare_at_price'] !== '' &&
            (!is_numeric($data['compare_at_price']) || (float) $data['compare_at_price'] < 0)
        ) {
            $errors[] = 'Harga pembanding tidak valid.';
        }

        if ($categoryInput !== '' && ($categoryId === false || !Category::find((int) $categoryId))) {
            $errors[] = 'Kategori produk tidak valid.';
        }

        if ($data['image_url'] !== '' && !$this->validImageUrl($data['image_url'])) {
            $errors[] = 'URL gambar tidak valid.';
        }

        if ($isUpdate) {
            $allowedStatus = ['active', 'draft', 'archived'];
            if (!in_array($data['status'], $allowedStatus, true)) {
                $errors[] = 'Status produk tidak valid.';
            }
        }

        return $errors;
    }

    /**
     * Memvalidasi bahwa URL gambar menggunakan skema http atau https.
     *
     * @param string $url
     * @return bool
     */
    private function validImageUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }
}
