<?php

class Product
{
    /**
     * Mengambil semua produk aktif dengan filter opsional.
     *
     * @param array $filters Mendukung key: q (keyword), category (slug), sort, min_price, max_price.
     * @return array
     */
    public static function all(array $filters = []): array
    {
        $where  = ["p.status = 'active'"];
        $params = [];

        // Filter pencarian keyword.
        if (!empty($filters['q'])) {
            $where[]  = "(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
            $like     = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // Filter berdasarkan slug kategori.
        if (!empty($filters['category'])) {
            $where[]  = "c.slug = ?";
            $params[] = $filters['category'];
        }

        // Filter rentang harga.
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[]  = "p.price >= ?";
            $params[] = (float) $filters['min_price'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[]  = "p.price <= ?";
            $params[] = (float) $filters['max_price'];
        }

        // Pengurutan.
        $order = match ($filters['sort'] ?? '') {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name'       => 'p.name ASC',
            default      => 'p.is_featured DESC, p.created_at DESC',
        };

        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY {$order}";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Mengambil produk unggulan (is_featured = 1).
     *
     * @param int $limit Jumlah produk yang diambil.
     * @return array
     */
    public static function featured(int $limit = 4): array
    {
        $stmt = db()->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = 'active' AND p.is_featured = 1
             ORDER BY p.created_at DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Menemukan produk aktif berdasarkan slug URL.
     *
     * @param string $slug
     * @return array|null
     */
    public static function findBySlug(string $slug): ?array
    {
        $stmt = db()->prepare(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'active'
             LIMIT 1"
        );
        $stmt->execute([$slug]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Menemukan produk berdasarkan ID (semua status, termasuk draft/archived).
     *
     * @param int $id
     * @return array|null
     */
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Mengambil semua produk untuk halaman admin (semua status).
     *
     * @return array
     */
    public static function adminAll(): array
    {
        return db()->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC"
        )->fetchAll();
    }

    /**
     * Membuat produk baru.
     *
     * BUG FIX (M9): Sebelumnya slug selalu di-append dengan random hex,
     * sehingga URL tidak predictable dan tidak SEO-friendly.
     * Sekarang slug di-generate dari nama, lalu di-cek duplikat.
     * Jika duplikat, tambahkan suffix angka (-2, -3, dst.).
     *
     * @param array $d Data produk.
     * @return int     ID produk baru.
     */
    public static function create(array $d): int
    {
        $baseSlug = slugify($d['name']);
        $slug     = self::uniqueSlug($baseSlug);

        $stmt = db()->prepare(
            "INSERT INTO products
                (category_id, name, slug, sku, description, price,
                 compare_at_price, stock, color, tag, image_url,
                 status, is_featured)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)"
        );

        $stmt->execute([
            $d['category_id'] ?: null,
            $d['name'],
            $slug,
            $d['sku'],
            $d['description'],
            $d['price'],
            $d['compare_at_price'] ?: null,
            $d['stock'],
            $d['color'],
            $d['tag'] ?: null,
            $d['image_url'],
            !empty($d['is_featured']) ? 1 : 0,
        ]);

        return (int) db()->lastInsertId();
    }

    /**
     * Memperbarui data produk.
     *
     * @param int   $id   ID produk.
     * @param array $data Data yang akan diperbarui.
     */
    public static function update(int $id, array $data): void
    {
        $stmt = db()->prepare(
            "UPDATE products SET
                category_id      = ?,
                name             = ?,
                sku              = ?,
                description      = ?,
                price            = ?,
                compare_at_price = ?,
                stock            = ?,
                color            = ?,
                tag              = ?,
                image_url        = ?,
                status           = ?,
                is_featured      = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $data['category_id'] ?: null,
            $data['name'],
            $data['sku'],
            $data['description'],
            $data['price'],
            $data['compare_at_price'] ?: null,
            $data['stock'],
            $data['color'],
            $data['tag'] ?: null,
            $data['image_url'],
            $data['status'],
            !empty($data['is_featured']) ? 1 : 0,
            $id,
        ]);
    }

    /**
     * Mengarsipkan produk (soft-delete).
     *
     * @param int $id ID produk.
     */
    public static function delete(int $id): void
    {
        db()->prepare(
            "UPDATE products SET status = 'archived' WHERE id = ?"
        )->execute([$id]);
    }

    /**
     * Membatalkan arsip produk (mengembalikan status menjadi 'active' agar kembali dijual).
     *
     * @param int $id ID produk.
     */
    public static function restore(int $id): void
    {
        db()->prepare(
            "UPDATE products SET status = 'active' WHERE id = ?"
        )->execute([$id]);
    }

    /**
     * Membuat slug unik berdasarkan slug dasar.
     * Menambahkan suffix angka (-2, -3, dst.) jika slug sudah digunakan.
     *
     * @param string $baseSlug Slug awal dari nama produk.
     * @return string          Slug unik yang belum digunakan.
     */
    private static function uniqueSlug(string $baseSlug): string
    {
        $slug   = $baseSlug;
        $suffix = 2;

        while (self::slugExists($slug)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * Memeriksa apakah slug sudah digunakan oleh produk lain.
     *
     * @param string $slug
     * @return bool
     */
    private static function slugExists(string $slug): bool
    {
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM products WHERE slug = ?"
        );
        $stmt->execute([$slug]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
