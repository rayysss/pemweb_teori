<?php
/**
 * FIX (C4): File ini sebelumnya terpotong di baris 45 — form tambah produk
 * tidak memiliki field tag, deskripsi, is_featured, dan tombol submit.
 * Tabel daftar produk juga tidak ada. File ini sekarang dilengkapi.
 */
$statusLabels = [
    'active'   => 'Aktif',
    'draft'    => 'Draft',
    'archived' => 'Diarsipkan',
];
?>
<section class="container standard-page admin-page">
    <div class="page-title">
        <span class="eyebrow">ZORA MANAGEMENT</span>
        <h1>Kelola produk</h1>
        <p>Tambah produk baru atau arsipkan produk yang tidak lagi dijual.</p>
    </div>

    <div class="admin-product-layout">
        <!-- Form Tambah Produk -->
        <form class="form-card" method="post" action="<?= base_url('admin/products/create') ?>">
            <?= csrf_field() ?>
            <h3>Tambah produk</h3>

            <label>Nama produk
                <input name="name" required placeholder="Contoh: Nova Phone Pro">
            </label>

            <label>SKU
                <input name="sku" required placeholder="Contoh: ZOR-PHN-005">
            </label>

            <label>Kategori
                <select name="category_id">
                    <option value="">Tanpa kategori</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="form-grid">
                <label>Harga (Rp)
                    <input type="number" name="price" min="1" required placeholder="0">
                </label>
                <label>Harga pembanding (Rp)
                    <input type="number" name="compare_at_price" min="0" placeholder="Opsional">
                </label>
                <label>Stok
                    <input type="number" name="stock" min="0" value="0">
                </label>
                <label>Warna
                    <input name="color" placeholder="Contoh: Black, Silver">
                </label>
            </div>

            <label>Tag produk
                <input name="tag" placeholder="Contoh: NEW, POPULAR, SALE">
            </label>

            <label>URL gambar
                <input type="url" name="image_url" placeholder="https://...">
            </label>

            <label>Deskripsi produk
                <textarea name="description" rows="4"
                    placeholder="Deskripsikan produk ini..."></textarea>
            </label>

            <label class="checkbox-label">
                <input type="checkbox" name="is_featured" value="1">
                <span>Tampilkan sebagai produk unggulan di homepage</span>
            </label>

            <button class="btn-zora w-100" type="submit">Tambah produk →</button>
        </form>

        <!-- Daftar Produk -->
        <div class="admin-product-list">
            <h3>Semua produk (<?= count($products) ?>)</h3>

            <?php if (!$products): ?>
                <div class="empty-state compact">
                    <p>Belum ada produk. Tambahkan produk pertama menggunakan form di samping.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table zora-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th style="min-width:180px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr class="<?= $p['status'] === 'archived' ? 'row-archived' : '' ?>">
                                    <td>
                                        <div class="admin-product-name">
                                            <?php if ($p['image_url']): ?>
                                                <img src="<?= e($p['image_url']) ?>"
                                                     alt=""
                                                     width="40" height="40"
                                                     style="object-fit:cover;border-radius:3px">
                                            <?php endif; ?>
                                            <span><?= e($p['name']) ?></span>
                                        </div>
                                    </td>
                                    <td><code><?= e($p['sku']) ?></code></td>
                                    <td><?= e($p['category_name'] ?? '—') ?></td>
                                    <td><?= rupiah($p['price']) ?></td>
                                    <td><?= (int) $p['stock'] ?></td>
                                    <td>
                                        <span class="status-pill status-<?= e($p['status']) ?>">
                                            <?= e($statusLabels[$p['status']] ?? $p['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-actions">
                                            <a href="<?= base_url('admin/products/edit/' . (int) $p['id']) ?>"
                                               class="btn-zora btn-small btn-edit">Edit</a>

                                            <?php if ($p['status'] !== 'archived'): ?>
                                                <form method="post"
                                                      action="<?= base_url('admin/products/delete') ?>"
                                                      onsubmit="return confirm('Arsipkan produk ini?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                                    <button class="btn-zora btn-small btn-archive" type="submit">Arsipkan</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post"
                                                      action="<?= base_url('admin/products/restore') ?>"
                                                      onsubmit="return confirm('Aktifkan kembali produk ini ke katalog?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                                    <button class="btn-zora btn-small btn-unarchive" type="submit">Batal Arsip</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
