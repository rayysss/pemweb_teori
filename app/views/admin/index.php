<?php
/**
 * Label status pesanan dalam Bahasa Indonesia.
 * FIX (L5): Sebelumnya status ditampilkan dalam bahasa Inggris (pending, confirmed, dll.)
 * Sekarang menggunakan label yang lebih informatif untuk admin.
 */
$statusLabels = [
    'pending'    => 'Menunggu',
    'confirmed'  => 'Dikonfirmasi',
    'processing' => 'Diproses',
    'shipped'    => 'Dikirim',
    'completed'  => 'Selesai',
    'cancelled'  => 'Dibatalkan',
];

$statusOptions = [
    'confirmed'  => 'Dikonfirmasi',
    'processing' => 'Diproses',
    'shipped'    => 'Dikirim',
    'completed'  => 'Selesai',
    'cancelled'  => 'Dibatalkan',
];
?>
<section class="container standard-page admin-page">
    <div class="page-title">
        <span class="eyebrow">ZORA MANAGEMENT</span>
        <h1>Dashboard admin</h1>
        <p>Ringkasan aktivitas toko ZORA.</p>
    </div>

    <div class="admin-stats">
        <div>
            <small>Produk aktif</small>
            <strong><?= number_format($stats['products']) ?></strong>
        </div>
        <div>
            <small>Total pesanan</small>
            <strong><?= number_format($stats['orders']) ?></strong>
        </div>
        <div>
            <small>Pelanggan</small>
            <strong><?= number_format($stats['customers']) ?></strong>
        </div>
        <div>
            <small>Omzet terkonfirmasi</small>
            <strong><?= rupiah($stats['revenue']) ?></strong>
        </div>
    </div>

    <div class="admin-toolbar">
        <h3>Pesanan terbaru</h3>
        <a class="btn-zora btn-small" href="<?= base_url('admin/products') ?>">Kelola produk →</a>
    </div>

    <div class="table-responsive">
        <table class="table zora-table">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ubah status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <?php $currentStatus = $o['order_status']; ?>
                    <tr>
                        <td><?= e($o['order_number']) ?></td>
                        <td><?= e($o['customer_name'] ?? 'Pelanggan') ?></td>
                        <td><?= rupiah($o['total_amount']) ?></td>
                        <td>
                            <span class="status-pill status-<?= e($currentStatus) ?>">
                                <?= e($statusLabels[$currentStatus] ?? ucfirst($currentStatus)) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($currentStatus !== 'cancelled'): ?>
                                <form class="status-form" method="post"
                                      action="<?= base_url('admin/orders/update') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>">
                                    <select name="status" aria-label="Ubah status pesanan <?= e($o['order_number']) ?>">
                                        <?php foreach ($statusOptions as $value => $label): ?>
                                            <option value="<?= e($value) ?>"
                                                <?= $value === $currentStatus ? 'selected' : '' ?>>
                                                <?= e($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn-zora btn-small" type="submit">Simpan</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:12px">Dibatalkan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
