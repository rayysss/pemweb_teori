<header class="site-header">
  <div class="container header-inner">
    <!-- Brand Logo -->
    <a class="brand" href="<?= base_url('home') ?>" aria-label="ZORA Home">ZORA<span>.</span></a>

    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false">
      <span></span>
      <span></span>
    </button>

    <!-- Main Navigation -->
    <nav class="main-nav">
      <a href="<?= base_url('products') ?>">Semua Produk</a>
      <a href="<?= base_url('products?category=smartphones') ?>">Smartphones</a>
      <a href="<?= base_url('products?category=computers') ?>">Computers</a>
      <a href="<?= base_url('products?category=audio') ?>">Audio</a>
      <a href="<?= base_url('products?category=wearables') ?>">Wearables</a>
      <a href="<?= base_url('products?category=cameras') ?>">Cameras</a>
      <a href="<?= base_url('products?category=accessories') ?>">Accessories</a>
    </nav>

    <!-- Header Actions -->
    <div class="header-actions">

      <?php if (is_logged_in()): ?>
        <a class="header-account" href="<?= base_url(is_admin() ? 'admin' : 'account') ?>">
          <?= e(explode(' ', current_user()['name'])[0]) ?>
        </a>
      <?php else: ?>
        <a class="header-account" href="<?= base_url('login') ?>">Masuk</a>
      <?php endif; ?>

      <a class="header-cart" href="<?= base_url('cart') ?>" aria-label="Keranjang">
        Keranjang <span><?= (int) $cartCount ?></span>
      </a>
    </div>
  </div>
</header>