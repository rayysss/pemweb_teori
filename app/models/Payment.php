<?php

class Payment
{
    /**
     * Mencatat data pembayaran awal untuk sebuah pesanan.
     *
     * @param int    $orderId  ID pesanan.
     * @param string $method   Metode pembayaran.
     * @param float  $amount   Total nominal pembayaran.
     * @return int             ID payment yang baru dibuat.
     */
    public static function create(int $orderId, string $method, float $amount): int
    {
        $allowed = ['bank_transfer', 'qris', 'ewallet', 'cod'];

        // Fallback ke bank_transfer jika method tidak dikenal.
        if (!in_array($method, $allowed, true)) {
            $method = 'bank_transfer';
        }

        $stmt = db()->prepare(
            "INSERT INTO payments (order_id, payment_method, payment_status, amount)
             VALUES (?, ?, 'pending', ?)"
        );
        $stmt->execute([$orderId, $method, $amount]);

        return (int) db()->lastInsertId();
    }

    /**
     * Mengambil data pembayaran terbaru untuk sebuah pesanan.
     *
     * @param int $orderId ID pesanan.
     * @return array|null  Data pembayaran atau null jika tidak ada.
     */
    public static function forOrder(int $orderId): ?array
    {
        $stmt = db()->prepare(
            "SELECT * FROM payments
             WHERE order_id = ?
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$orderId]);

        return $stmt->fetch() ?: null;
    }
}
