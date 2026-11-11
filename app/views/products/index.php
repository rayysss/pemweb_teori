<section class="page-banner">
    <div class="container">
        <span class="eyebrow">KATALOG ZORA</span>
        <h1>Temukan perangkat yang tepat.</h1>
        <p>Jelajahi koleksi elektronik untuk kebutuhanmu.</p>
    </div>
</section>

<section class="container catalog-section">
    <form class="filter-bar" method="get" action="<?= base_url('products') ?>">
        <input type="hidden" name="url" value="products">
        
        <label class="search-field">
            <span>⌕</span>
            <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Cari produk...">
        </label>
        
        <select name="category" aria-label="Kategori">
            <option value="">Semua kategori</option>
            <?php foreach($categories as $c): ?>
                <option value="<?= e($c['slug']) ?>" <?= $filters['category']===$c['slug']?'selected':'' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <select name="sort" aria-label="Urutkan">
            <option value="">Rekomendasi</option>
            <option value="price_asc" <?= $filters['sort']==='price_asc'?'selected':'' ?>>Harga terendah</option>
            <option value="price_desc" <?= $filters['sort']==='price_desc'?'selected':'' ?>>Harga tertinggi</option>
            <option value="name" <?= $filters['sort']==='name'?'selected':'' ?>>Nama A–Z</option>
        </select>
        
        <button class="btn-zora btn-small">Terapkan</button>
    </form>

    <div class="catalog-meta">
        <span><?= count($products) ?> produk ditemukan</span>
        <a href="<?= base_url('products') ?>">Reset filter</a>
    </div>

    <?php if (!$products): ?>
        <div class="empty-state compact">
            <h3>Produk belum ditemukan</h3>
            <p>Coba kata kunci atau kategori yang berbeda.</p>
            <a href="<?= base_url('products') ?>" class="text-link">Lihat semua produk →</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <a class="product-image" href="<?= base_url('products/' . $product['slug']) ?>">
                        <img src="<?= e(product_image($product['image_url'])) ?>"
                             alt="<?= e($product['name']) ?>"
                             loading="lazy">
                        <?php if ($product['tag']): ?>
                            <span class="product-tag"><?= e($product['tag']) ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="product-info">
                        <small><?= e($product['category_name'] ?? 'Electronics') ?></small>
                        <a class="product-name"
                           href="<?= base_url('products/' . $product['slug']) ?>">
                           <?= e($product['name']) ?>
                        </a>
                        <strong><?= rupiah($product['price']) ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>