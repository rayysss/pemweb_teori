<section class="container standard-page">
    <div class="page-title">
        <span class="eyebrow">ZORA BAG</span>
        <h1>Keranjang belanja</h1>
        <p>Periksa kembali produk pilihanmu sebelum melanjutkan.</p>
    </div>

    <?php if(!$items): ?>
        <div class="empty-state compact">
            <h3>Keranjangmu masih kosong</h3>
            <p>Temukan perangkat yang sesuai dengan kebutuhanmu.</p>
            <a class="btn-zora" href="<?= base_url('products') ?>">Mulai belanja →</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-list">
                <?php foreach($items as $item): ?>
                    <div class="cart-item">
                        <img src="<?= e(product_image($item['image_url'])) ?>" alt="<?= e($item['name']) ?>">
                        <div class="cart-item-info">
                            <a href="<?= base_url('products/'.$item['slug']) ?>"><?= e($item['name']) ?></a>
                            <small><?= rupiah($item['unit_price']) ?></small>
                            <form method="post" action="<?= base_url('cart/update') ?>" class="quantity-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="item_id" value="<?= (int)$item['item_id'] ?>">
                                <input type="number" name="quantity" min="1" max="<?= (int)$item['stock'] ?>" value="<?= (int)$item['quantity'] ?>">
                                <button type="submit" class="subtle-button">Perbarui</button>
                            </form>
                        </div>
                        <div class="cart-item-total">
                            <strong><?= rupiah($item['unit_price']*$item['quantity']) ?></strong>
                            <form method="post" action="<?= base_url('cart/remove') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="item_id" value="<?= (int)$item['item_id'] ?>">
                                <button class="remove-button">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <aside class="summary-card">
                <h3>Ringkasan</h3>
                <div>
                    <span>Subtotal</span>
                    <strong><?= rupiah($subtotal) ?></strong>
                </div>
                <div>
                    <span>Pengiriman</span>
                    <strong><?= $subtotal>=500000?'Gratis':rupiah(15000) ?></strong>
                </div>
                <p class="summary-hint">Gratis ongkir untuk belanja minimal Rp 500.000.</p>
                <div class="summary-total">
                    <span>Total sementara</span>
                    <strong><?= rupiah($subtotal+($subtotal>=500000?0:15000)) ?></strong>
                </div>
                <a class="btn-zora w-100" href="<?= base_url('checkout') ?>">Lanjut checkout →</a>
            </aside>
        </div>
    <?php endif; ?>
</section>