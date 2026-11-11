<?php

class Order
{
    /**
     * Membuat pesanan baru dari isi cart aktif.
     *
     * BUG FIX (C1/M12): Sebelumnya Cart::clear() dipanggil SETELAH
     * $pdo->commit(). Jika Cart::clear() gagal, pesanan sudah tersimpan
     * tapi cart tidak dikosongkan → state inconsistency.
     *
     * FIX: Cart::clear() sekarang dipanggil di dalam transaksi menggunakan
     * PDO yang sama, sehingga jika gagal, seluruh transaksi di-rollback.
     *
     * @throws RuntimeException Jika cart kosong atau stok tidak mencukupi.
     */
    public static function createFromCart(
        int    $userId,
        array  $shipping,
        string $paymentMethod
    ): int {
        $pdo   = db();
        $items = Cart::items();

        if (!$items) {
            throw new RuntimeException('Keranjang masih kosong.');
        }

        $subtotal     = Cart::subtotal();
        $shippingCost = $subtotal >= SHIPPING_FREE_THRESHOLD ? 0 : SHIPPING_FLAT_COST;
        $total        = $subtotal + $shippingCost;

        $pdo->beginTransaction();

        try {
            // 1. Validasi stok setiap item dengan row lock (FOR UPDATE).
            $checkStmt = $pdo->prepare(
                "SELECT stock, status
                 FROM products
                 WHERE id = ?
                 FOR UPDATE"
            );

            foreach ($items as $item) {
                $checkStmt->execute([$item['product_id']]);
                $stock = $checkStmt->fetch();

                if (
                    !$stock ||
                    $stock['status'] !== 'active' ||
                    (int) $stock['stock'] < (int) $item['quantity']
                ) {
                    throw new RuntimeException(
                        'Stok ' . $item['name'] . ' tidak mencukupi.'
                    );
                }
            }

            // 2. Buat nomor pesanan unik.
            $orderNumber = 'ZOR' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));

            // 3. Insert record pesanan.
            $insertOrder = $pdo->prepare(
                "INSERT INTO orders (
                    order_number, user_id,
                    recipient_name, recipient_phone,
                    shipping_address, shipping_city,
                    shipping_province, shipping_postal_code,
                    subtotal, shipping_cost, discount_amount,
                    total_amount, order_status, customer_note
                ) VALUES (
                    ?, ?,
                    ?, ?,
                    ?, ?,
                    ?, ?,
                    ?, ?, 0,
                    ?, 'pending', ?
                )"
            );
            $insertOrder->execute([
                $orderNumber,
                $userId,
                $shipping['recipient_name'],
                $shipping['recipient_phone'],
                $shipping['address'],
                $shipping['city'],
                $shipping['province'],
                $shipping['postal_code'],
                $subtotal,
                $shippingCost,
                $total,
                $shipping['note'],
            ]);

            $orderId = (int) $pdo->lastInsertId();

            // 4. Insert order items dan kurangi stok.
            $insertItem = $pdo->prepare(
                "INSERT INTO order_items
                    (order_id, product_id, product_name, product_sku,
                     product_image, unit_price, quantity, line_total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $reduceStock = $pdo->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ?"
            );

            foreach ($items as $item) {
                $lineTotal = (float) $item['unit_price'] * (int) $item['quantity'];

                $insertItem->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['sku'] ?? null,
                    $item['image_url'],
                    $item['unit_price'],
                    $item['quantity'],
                    $lineTotal,
                ]);

                $reduceStock->execute([
                    $item['quantity'],
                    $item['product_id'],
                ]);
            }

            // 5. Catat pembayaran.
            Payment::create($orderId, $paymentMethod, $total);

            // 6. FIX: Kosongkan cart di DALAM transaksi yang sama,
            //    dengan meneruskan PDO connection yang sudah di-begin.
            //    Jika ini gagal, seluruh transaksi akan di-rollback.
            Cart::clear(true, $pdo);

            // 7. Commit seluruh transaksi.
            $pdo->commit();

            return $orderId;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Mengambil semua pesanan milik seorang user.
     */
    public static function forUser(int $userId): array
    {
        $stmt = db()->prepare(
            "SELECT * FROM orders
             WHERE user_id = ?
             ORDER BY ordered_at DESC"
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    /**
     * Mengambil semua pesanan (untuk dashboard admin).
     */
    public static function all(): array
    {
        return db()->query(
            "SELECT o.*, u.name AS customer_name
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.ordered_at DESC"
        )->fetchAll();
    }

    /**
     * Mengambil detail pesanan milik user tertentu (proteksi IDOR).
     */
    public static function findForUser(int $id, int $userId): ?array
    {
        $stmt = db()->prepare(
            "SELECT * FROM orders
             WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$id, $userId]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Mengambil semua item dari satu pesanan.
     */
    public static function items(int $orderId): array
    {
        $stmt = db()->prepare(
            "SELECT * FROM order_items WHERE order_id = ?"
        );
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }

    /**
     * Memperbarui status pesanan.
     * Jika status diubah ke 'cancelled', stok produk dikembalikan.
     *
     * SECURITY FIX (H7): Exception message tidak lagi dikirim langsung
     * ke user — cukup di-rethrow, dan caller (AdminController) yang
     * bertanggung jawab menampilkan pesan generik ke user.
     *
     * @throws InvalidArgumentException Jika status tidak valid.
     * @throws RuntimeException         Jika pesanan tidak ditemukan atau tidak bisa diubah.
     */
    public static function updateStatus(int $id, string $status): void
    {
        $allowed = ['confirmed', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('Status pesanan tidak valid.');
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "SELECT order_status FROM orders WHERE id = ? FOR UPDATE"
            );
            $stmt->execute([$id]);
            $currentStatus = $stmt->fetchColumn();

            if ($currentStatus === false) {
                throw new RuntimeException('Pesanan tidak ditemukan.');
            }

            if ($currentStatus === 'cancelled' && $status !== 'cancelled') {
                throw new RuntimeException(
                    'Pesanan yang sudah dibatalkan tidak dapat diaktifkan kembali.'
                );
            }

            // Kembalikan stok jika pesanan dibatalkan.
            if ($status === 'cancelled' && $currentStatus !== 'cancelled') {
                $itemStmt = $pdo->prepare(
                    "SELECT product_id, quantity
                     FROM order_items
                     WHERE order_id = ? AND product_id IS NOT NULL"
                );
                $itemStmt->execute([$id]);

                $restoreStmt = $pdo->prepare(
                    "UPDATE products SET stock = stock + ? WHERE id = ?"
                );

                foreach ($itemStmt->fetchAll() as $item) {
                    $restoreStmt->execute([$item['quantity'], $item['product_id']]);
                }
            }

            $pdo->prepare(
                "UPDATE orders SET order_status = ? WHERE id = ?"
            )->execute([$status, $id]);

            $pdo->commit();

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }
}
