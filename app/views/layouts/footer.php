</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <div>
            <a class="brand brand-light" href="<?= base_url() ?>" aria-label="ZORA Home">ZORA<span>.</span></a>
            <p>Teknologi untuk keseharian yang lebih sederhana.</p>
        </div>

        <div class="footer-links">
            <a href="<?= base_url('products') ?>">Produk</a>
            <a href="<?= base_url('orders') ?>">Pesanan</a>
            <a href="<?= base_url('account') ?>">Akun</a>
        </div>

        <small>© <?= date('Y') ?> ZORA Electronics. All rights reserved.</small>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- FIX (M4): Tambahkan defer pada semua script agar tidak memblokir rendering HTML. -->
<!-- FIX (M3): main.js di-load di semua halaman (wajar untuk interaksi global),
     sedangkan cart.js, filter.js, checkout.js hanya relevan di halaman tertentu.
     Dengan 'defer' ketiganya di-parse setelah HTML, overhead minimal. -->
<script src="<?= asset('js/main.js') ?>" defer></script>
<script src="<?= asset('js/cart.js') ?>" defer></script>
<script src="<?= asset('js/filter.js') ?>" defer></script>
<script src="<?= asset('js/checkout.js') ?>" defer></script>

</body>
</html>
