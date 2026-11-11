<?php
/**
 * FIX (L6): Status pesanan sebelumnya hanya ucfirst() — tidak rapi jika
 * status mengandung underscore (bank_transfer dll.).
 * Sekarang menggunakan mapping label Indonesia yang konsisten.
 */
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
        <span class="eyebrow">AKUN ZORA</span>
        <h1>Pesanan saya</h1>
        <p>Semua transaksi yang pernah kamu buat.</p>
    </div>

    <?php if (!$orders): ?>
        <div class="empty-state compact">
            <h3>Belum ada pesanan</h3>
            <p>Yuk, mulai belanja dan temukan perangkat yang kamu butuhkan.</p>
            <a class="btn-zora" href="<?= base_url('products') ?>">Mulai belanja →</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table zora-table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= e($o['order_number']) ?></td>
                            <td><?= date('d M Y', strtotime($o['ordered_at'])) ?></td>
                            <td><?= rupiah($o['total_amount']) ?></td>
                            <td>
                                <span class="status-pill status-<?= e($o['order_status']) ?>">
                                    <?= e($statusLabels[$o['order_status']] ?? ucfirst(str_replace('_', ' ', $o['order_status']))) ?>
                                </span>
                            </td>
                            <td><a href="<?= base_url('orders/' . $o['id']) ?>" class="text-link">Detail →</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
