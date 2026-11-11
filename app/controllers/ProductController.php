<?php
/**
 * Controller Produk: Menangani katalog belanja, pencarian keyword, filter kategori,
 * pengurutan harga, serta tampilan halaman detail barang.
 */
class ProductController
{
    /**
     * Menampilkan katalog seluruh produk dengan dukungan pencarian dan pengurutan.
     */
    public function index(): void
    {
        // Membaca parameter query string dari URL (misal: /products?q=phone&category=smartphones).
        $keyword  = $this->getString('q');
        $category = $this->getString('category');
        $sort     = $this->getString('sort');

        // Whitelist pengurutan untuk mencegah manipulasi query database.
        $allowedSorts = ['price_asc', 'price_desc', 'name'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = '';
        }

        $filters = [
            'q'        => $keyword,
            'category' => $category,
            'sort'     => $sort,
        ];

        // Mengambil data produk sesuai filter yang aktif.
        $products   = Product::all($filters);
        $categories = Category::all();

        render('products/index', [
            'pageTitle'  => 'Semua Produk',
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    /**
     * Menampilkan detail spesifikasi satu produk berdasarkan slug URL yang unik.
     */
    public function show(string $slug): void
    {
        $slug = trim($slug);

        // Jika slug kosong, kirim response 404 Not Found.
        if ($slug === '') {
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Produk tidak ditemukan']);
            return;
        }

        // Cari data produk aktif berdasarkan slug.
        $product = Product::findBySlug($slug);

        // Jika tidak ditemukan atau produk tidak berstatus aktif, tampilkan 404.
        if (!$product) {
            http_response_code(404);
            render('home/not-found', ['pageTitle' => 'Produk tidak ditemukan']);
            return;
        }

        render('products/detail', [
            'pageTitle' => $product['name'],
            'product'   => $product,
        ]);
    }

    /**
     * Mengambil dan membersihkan parameter GET bertipe string dari URL.
     */
    private function getString(string $key): string
    {
        $value = $_GET[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }
}
