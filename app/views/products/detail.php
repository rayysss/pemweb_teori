<section class="container product-detail">
    <div class="detail-image">
        <img src="<?= e(product_image($product['image_url'])) ?>" alt="<?= e($product['name']) ?>">
    </div>
    <div class="detail-copy">
        <div class="breadcrumbs">
            <a href="<?= base_url('home') ?>">Beranda</a> / <a href="<?= base_url('products') ?>">Produk</a> / <?= e($product['name']) ?>
        </div>
        <span class="eyebrow"><?= e($product['category_name']??'ELECTRONICS') ?></span>
        <h1><?= e($product['name']) ?></h1>
        <div class="detail-price">
            <?= rupiah($product['price']) ?>
            <?php if($product['compare_at_price']): ?>
                <del><?= rupiah($product['compare_at_price']) ?></del>
            <?php endif; ?>
        </div>
        <p><?= nl2br(e($product['description'])) ?></p>
        <div class="detail-spec">
            <span>Warna</span>
            <strong><?= e($product['color']?:'—') ?></strong>
            <span>Ketersediaan</span>
            <strong><?= (int)$product['stock']>0?'Tersedia':'Stok habis' ?></strong>
        </div>

        <?php if((int)$product['stock']>0): ?>
            <form method="post" action="<?= base_url('cart/add') ?>" class="add-cart-form">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <label>Jumlah 
                    <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>">
                </label>
                <button class="btn-zora">Tambah ke keranjang <span>→</span></button>
            </form>
        <?php else: ?>
            <button class="btn-zora" disabled>Stok habis</button>
        <?php endif; ?>
        
        <small class="stock-note">SKU: <?= e($product['sku']) ?></small>
    </div>
</section>