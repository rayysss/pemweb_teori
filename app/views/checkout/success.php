<section class="container empty-state order-success">
    <span class="success-mark">✓</span>
    <span class="eyebrow">PESANAN DITERIMA</span>
    <h1>Terima kasih sudah memilih ZORA.</h1>
    <p>Pesanan <strong><?= e($order['order_number']) ?></strong> berhasil dibuat. Status awal pesanan adalah menunggu konfirmasi.</p>
    <a class="btn-zora" href="<?= base_url('orders') ?>">Lihat pesanan →</a>
</section>