<?php
/**
 * Controller Beranda (Home): Menyiapkan data awal untuk pengunjung website.
 */
class HomeController
{
    /**
     * Menampilkan halaman utama (homepage) ZORA.
     * Mengambil 4 produk unggulan dan daftar kategori untuk ditampilkan di slider/grid.
     */
    public function index(): void
    {
        // Mengambil produk pilihan dengan status aktif dan is_featured = 1.
        $featuredProducts = Product::featured(4);

        // Mengambil seluruh kategori produk aktif beserta jumlah barangnya.
        $categories = Category::all();

        // Me-render tampilan template home/index dengan data pendukung.
        render('home/index', [
            'pageTitle'  => 'ZORA — Electronics for Everyday Life',
            'featured'   => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}
