<?php
// Deteksi segmen URL aktif untuk active state navigasi.
$_currentPath = trim((string) ($_GET['url'] ?? ''), '/');
$_currentFirst = explode('/', $_currentPath)[0] ?? '';
?>
<header class="site-header">
  <div class="container header-inner">
    <!-- Brand Logo -->
    <a class="brand" href="<?= base_url() ?>" aria-label="ZORA Home">ZORA<span>.</span></a>

    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false">
      <span></span>
      <span></span>
    </button>

    <!-- Main Navigation -->
    <nav class="main-nav" role="navigation" aria-label="Navigasi utama">
      <a href="<?= base_url('products') ?>"
         class="<?= $_currentFirst === 'products' && empty($_GET['category']) ? 'nav-active' : '' ?>">
         Semua Produk
      </a>
      <a href="<?= base_url('products?category=smartphones') ?>"
         class="<?= ($_GET['category'] ?? '') === 'smartphones' ? 'nav-active' : '' ?>">
         Smartphones
      </a>
      <a href="<?= base_url('products?category=computers') ?>"
         class="<?= ($_GET['category'] ?? '') === 'computers' ? 'nav-active' : '' ?>">
         Computers
      </a>
      <a href="<?= base_url('products?category=audio') ?>"
         class="<?= ($_GET['category'] ?? '') === 'audio' ? 'nav-active' : '' ?>">
         Audio
      </a>
      <a href="<?= base_url('products?category=wearables') ?>"
         class="<?= ($_GET['category'] ?? '') === 'wearables' ? 'nav-active' : '' ?>">
         Wearables
      </a>
      <a href="<?= base_url('products?category=cameras') ?>"
         class="<?= ($_GET['category'] ?? '') === 'cameras' ? 'nav-active' : '' ?>">
         Cameras
      </a>
      <a href="<?= base_url('products?category=accessories') ?>"
         class="<?= ($_GET['category'] ?? '') === 'accessories' ? 'nav-active' : '' ?>">
         Accessories
      </a>
    </nav>

    <!-- Header Actions -->
    <div class="header-actions">

      <?php if (is_logged_in()): ?>
        <a class="header-account" href="<?= base_url(is_admin() ? 'admin' : 'account') ?>"
           aria-label="Akun <?= e(current_user()['name']) ?>">
          <?= e(explode(' ', current_user()['name'])[0]) ?>
        </a>
      <?php else: ?>
        <a class="header-account" href="<?= base_url('login') ?>">Masuk</a>
      <?php endif; ?>

      <a class="header-cart" href="<?= base_url('cart') ?>" aria-label="Keranjang belanja, <?= (int) $cartCount ?> item">
        <i class="bi bi-bag" aria-hidden="true"></i>
        <span class="cart-label">Keranjang</span>
        <span><?= (int) $cartCount ?></span>
      </a>
    </div>
  </div>
</header>