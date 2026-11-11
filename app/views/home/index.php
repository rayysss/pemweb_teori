<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">ELECTRONICS FOR EVERYDAY LIFE</span>
            <h1>Teknologi yang terasa <em>lebih sederhana.</em></h1>
            <p>Temukan perangkat pilihan untuk bekerja, belajar, dan menikmati setiap hari.</p>
            <a class="btn-zora" href="<?= base_url('products') ?>">Jelajahi Produk <span>→</span></a>
            <div class="hero-note">
                <span class="note-line"></span> Pilihan praktis. Kualitas terkurasi.
            </div>
        </div>
        <div class="hero-visual">
            <!-- FIX (H8): Hero image adalah LCP element — gunakan loading="eager" (default)
             dan fetchpriority="high" agar browser memprioritaskan load gambar ini. -->
        <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=1400&q=90"
             alt="Perangkat elektronik di atas meja kerja"
             fetchpriority="high"
             width="700" height="430">
            <div class="hero-chip">
                <strong>Everyday essentials</strong>
                <span>Temukan yang kamu butuhkan.</span>
            </div>
        </div>
    </div>
</section>

<section class="category-section container">
    <div class="section-heading">
        <div>
            <span class="eyebrow">PILIH SESUAI KEBUTUHAN</span>
            <h2>Jelajahi kategori</h2>
        </div>
        <a class="text-link" href="<?= base_url('products') ?>">Lihat semua <span>→</span></a>
    </div>
    <div class="category-grid">
        <?php foreach($categories as $category): ?>
            <a class="category-card" href="<?= base_url('products?category='.urlencode($category['slug'])) ?>">
                <span><?= e($category['name']) ?></span>
                <small><?= (int)$category['product_count'] ?> produk</small>
                <b>↗</b>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="featured-section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">DIPILIH UNTUKMU</span>
                <h2>Produk pilihan</h2>
                <p>Perangkat yang fungsional, tampil sederhana, dan nyaman digunakan.</p>
            </div>
            <a class="text-link" href="<?= base_url('products') ?>">Lihat katalog <span>→</span></a>
        </div>
        <div class="product-grid">
            <?php foreach($featured as $product): ?>
                <article class="product-card">
                    <a class="product-image" href="<?= base_url('products/'.$product['slug']) ?>">
                        <img src="<?= e(product_image($product['image_url'])) ?>" alt="<?= e($product['name']) ?>">
                        <?php if($product['tag']): ?>
                            <span class="product-tag"><?= e($product['tag']) ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="product-info">
                        <small><?= e($product['category_name']??'Electronics') ?></small>
                        <a href="<?= base_url('products/'.$product['slug']) ?>" class="product-name"><?= e($product['name']) ?></a>
                        <strong><?= rupiah($product['price']) ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="service-strip container">
    <div>
        <span>01</span>
        <strong>Produk terkurasi</strong>
        <small>Pilihan untuk kebutuhan harian.</small>
    </div>
    <div>
        <span>02</span>
        <strong>Belanja praktis</strong>
        <small>Proses sederhana dan jelas.</small>
    </div>
    <div>
        <span>03</span>
        <strong>Dukungan pelanggan</strong>
        <small>Kami siap membantu.</small>
    </div>
</section>