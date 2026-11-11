<?php

class Cart
{
    /**
     * Mendapatkan atau membuat cart aktif untuk user/session saat ini.
     * Mengembalikan cart ID (integer).
     */
    private static function cartId(): int
    {
        $pdo       = db();
        $userId    = current_user()['id'] ?? null;
        $sessionId = session_id();

        if ($userId) {
            $stmt = $pdo->prepare(
                "SELECT id FROM carts
                 WHERE user_id = ? AND status = 'active'
                 ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute([$userId]);
        } else {
            $stmt = $pdo->prepare(
                "SELECT id FROM carts
                 WHERE session_id = ? AND status = 'active'
                 ORDER BY id DESC LIMIT 1"
            );
            $stmt->execute([$sessionId]);
        }

        $id = $stmt->fetchColumn();

        if ($id) {
            return (int) $id;
        }

        // Buat cart baru jika belum ada.
        $insert = $pdo->prepare(
            "INSERT INTO carts (user_id, session_id, status)
             VALUES (?, ?, 'active')"
        );
        $insert->execute([$userId, $sessionId]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Mengambil semua item dalam cart aktif.
     */
    public static function items(): array
    {
        $cartId = self::cartId();

        $stmt = db()->prepare(
            "SELECT
                ci.id          AS item_id,
                ci.quantity,
                ci.unit_price,
                p.id           AS product_id,
                p.name,
                p.slug,
                p.sku,
                p.image_url,
                p.stock,
                p.status,
                p.price
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.cart_id = ?
             ORDER BY ci.created_at DESC"
        );
        $stmt->execute([$cartId]);

        return $stmt->fetchAll();
    }

    /**
     * Menambahkan produk ke cart.
     * Jika produk sudah ada, kuantitas ditambah (tidak melebihi stok).
     *
     * @throws RuntimeException Jika produk tidak aktif atau stok tidak cukup.
     */
    public static function add(int $productId, int $quantity = 1): void
    {
        $product = Product::find($productId);

        if (!$product || $product['status'] !== 'active') {
            throw new RuntimeException('Produk tidak tersedia.');
        }

        if ((int) $product['stock'] < $quantity) {
            throw new RuntimeException('Stok produk tidak mencukupi.');
        }

        $cartId = self::cartId();

        $stmt = db()->prepare(
            "INSERT INTO cart_items (cart_id, product_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                quantity = LEAST(quantity + VALUES(quantity), ?)"
        );
        $stmt->execute([
            $cartId,
            $productId,
            $quantity,
            $product['price'],
            $product['stock'],  // cap quantity agar tidak melebihi stok
        ]);
    }

    /**
     * Memperbarui kuantitas item dalam cart.
     *
     * SECURITY FIX (C2/C3): Sebelumnya query UPDATE tidak memfilter cart_id,
     * memungkinkan user A mengubah item milik user B (IDOR). Sekarang
     * query UPDATE juga menyertakan cart_id agar hanya item milik cart
     * yang aktif saat ini yang bisa diubah.
     *
     * @throws RuntimeException Jika item tidak ditemukan atau stok tidak cukup.
     */
    public static function update(int $itemId, int $quantity): void
    {
        $cartId = self::cartId();

        // Ambil item sekaligus validasi kepemilikan (harus belong ke cart ini).
        $stmt = db()->prepare(
            "SELECT ci.quantity, p.stock
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.id = ? AND ci.cart_id = ?"
        );
        $stmt->execute([$itemId, $cartId]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new RuntimeException('Item keranjang tidak ditemukan.');
        }

        if ($quantity > (int) $row['stock']) {
            throw new RuntimeException('Jumlah melebihi stok yang tersedia.');
        }

        // FIX: Tambahkan cart_id pada WHERE clause untuk mencegah IDOR.
        db()->prepare(
            "UPDATE cart_items
             SET quantity = ?
             WHERE id = ? AND cart_id = ?"
        )->execute([$quantity, $itemId, $cartId]);
    }

    /**
     * Menghapus item dari cart (sudah aman: filter cart_id).
     */
    public static function remove(int $itemId): void
    {
        $cartId = self::cartId();

        db()->prepare(
            "DELETE FROM cart_items
             WHERE id = ? AND cart_id = ?"
        )->execute([$itemId, $cartId]);
    }

    /**
     * Menghitung subtotal cart aktif.
     */
    public static function subtotal(): float
    {
        $total = 0.0;

        foreach (self::items() as $item) {
            $total += (float) $item['unit_price'] * (int) $item['quantity'];
        }

        return $total;
    }

    /**
     * Menghitung jumlah total produk dalam cart.
     */
    public static function count(): int
    {
        $total = 0;

        foreach (self::items() as $item) {
            $total += (int) $item['quantity'];
        }

        return $total;
    }

    /**
     * Mengosongkan cart.
     *
     * @param bool $convert Jika true, ubah status cart menjadi 'converted'.
     * @param PDO|null $pdo  Inject koneksi PDO (untuk digunakan dalam transaksi).
     */
    public static function clear(bool $convert = false, ?PDO $pdo = null): void
    {
        $pdo    = $pdo ?? db();
        $cartId = self::cartId();

        $pdo->prepare(
            "DELETE FROM cart_items WHERE cart_id = ?"
        )->execute([$cartId]);

        if ($convert) {
            $pdo->prepare(
                "UPDATE carts SET status = 'converted' WHERE id = ?"
            )->execute([$cartId]);
        }
    }

    /**
     * Menggabungkan guest cart ke cart user yang baru login.
     * Stok tidak divalidasi saat merge — validasi terjadi saat checkout.
     */
    public static function mergeGuestCart(int $userId, string $oldSessionId): void
    {
        $pdo = db();

        // Cari cart guest berdasarkan session ID lama.
        $stmt = $pdo->prepare(
            "SELECT id FROM carts
             WHERE session_id = ? AND status = 'active' AND user_id IS NULL
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$oldSessionId]);
        $guestCartId = $stmt->fetchColumn();

        if (!$guestCartId) {
            return;
        }

        // Cari cart user yang sudah login.
        $stmt = $pdo->prepare(
            "SELECT id FROM carts
             WHERE user_id = ? AND status = 'active'
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$userId]);
        $userCartId = $stmt->fetchColumn();

        if (!$userCartId) {
            // Tidak ada cart user → ambil alih cart guest.
            $pdo->prepare(
                "UPDATE carts SET user_id = ?, session_id = ? WHERE id = ?"
            )->execute([$userId, session_id(), $guestCartId]);

            return;
        }

        // User sudah punya cart → merge item dari guest ke user cart.
        $items = $pdo->prepare(
            "SELECT product_id, quantity, unit_price
             FROM cart_items
             WHERE cart_id = ?"
        );
        $items->execute([$guestCartId]);

        $insertStmt = $pdo->prepare(
            "INSERT INTO cart_items (cart_id, product_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                quantity = quantity + VALUES(quantity)"
        );

        foreach ($items->fetchAll() as $item) {
            $insertStmt->execute([
                $userCartId,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price'],
            ]);
        }

        // Tandai cart guest sebagai abandoned.
        $pdo->prepare(
            "UPDATE carts SET status = 'abandoned' WHERE id = ?"
        )->execute([$guestCartId]);
    }
}
