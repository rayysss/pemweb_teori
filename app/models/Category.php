<?php

class Category
{
    /**
     * Mengambil semua kategori beserta jumlah produk aktif di dalamnya.
     *
     * @param bool $activeOnly Jika true, hanya ambil kategori dengan is_active = 1.
     * @return array
     */
    public static function all(bool $activeOnly = true): array
    {
        $sql = "SELECT
                    c.*,
                    (
                        SELECT COUNT(*)
                        FROM products p
                        WHERE p.category_id = c.id
                          AND p.status = 'active'
                    ) AS product_count
                FROM categories c";

        if ($activeOnly) {
            $sql .= " WHERE c.is_active = 1";
        }

        $sql .= " ORDER BY c.sort_order, c.name";

        return db()->query($sql)->fetchAll();
    }

    /**
     * Menemukan kategori berdasarkan ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }
}
