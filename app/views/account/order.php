<?php
$statusLabels = [
    'pending'    => 'Menunggu',
    'confirmed'  => 'Dikonfirmasi',
    'processing' => 'Diproses',
    'shipped'    => 'Dikirim',
    'completed'  => 'Selesai',
    'cancelled'  => 'Dibatalkan',
];
?>
<section class="container standard-page">
    <div class="page-title">
        <span class="eyebrow">DETAIL PESANAN</span>
        <h1><?= e($order['order_number']) ?></h1>
        <p>Status:
            <span class="status-pill status-<?= e($order['order_status']) ?>">
                <?= e($statusLabels[$order['order_status']] ?? ucfirst($order['order_status'])) ?>
            </span>
        </p>
    </div>

    <div class="order-detail-grid">
        <div class="form-card">
            <h3>Produk dipesan</h3>
            <?php foreach($items as $item): ?>
                <div class="cart-item">
                    <img src="<?= e(product_image($item['product_image'])) ?>" alt="">
                    <div class="cart-item-info">
                        <strong><?= e($item['product_name']) ?></strong>
                        <small><?= (int)$item['quantity'] ?> × <?= rupiah($item['unit_price']) ?></small>
                    </div>
                    <strong><?= rupiah($item['line_total']) ?></strong>
                </div>
            <?php endforeach; ?>

            <h3 class="form-heading-spaced">Alamat pengiriman</h3>
            <p>
                <?= e($order['recipient_name']) ?> · <?= e($order['recipient_phone']) ?><br>
                <?= nl2br(e($order['shipping_address'])) ?><br>
                <?= e($order['shipping_city']) ?>, <?= e($order['shipping_province']) ?> <?= e($order['shipping_postal_code']) ?>
            </p>
        </div>

        <aside class="summary-card">
            <h3>Ringkasan</h3>
            <div>
                <span>Subtotal</span>
                <strong><?= rupiah($order['subtotal']) ?></strong>
            </div>
            <div>
                <span>Pengiriman</span>
                <strong><?= rupiah($order['shipping_cost']) ?></strong>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <strong><?= rupiah($order['total_amount']) ?></strong>
            </div>
            <p class="summary-hint">
                Pembayaran: <?= e(str_replace('_',' ',ucfirst($payment['payment_method']??'-'))) ?><br>
                Status pembayaran: <?= e($payment['payment_status']??'-') ?>
            </p>
        </aside>
    </div>
    <a href="<?= base_url('orders') ?>" class="text-link">← Kembali ke pesanan</a>
</section>