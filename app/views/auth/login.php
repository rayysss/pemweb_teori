<section class="container auth-page">
    <div class="auth-card">
        <span class="eyebrow">SELAMAT DATANG KEMBALI</span>
        <h1>Masuk ke ZORA</h1>
        <p>Akses pesanan dan pengalaman belanjamu.</p>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $err): ?>
                    <div><?= e($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>
            <label>Email
                <input type="email" name="email" required autocomplete="email" value="<?= old('email') ?>">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="btn-zora w-100">Masuk →</button>
        </form>
        
        <p class="auth-switch">Belum punya akun? <a href="<?= base_url('register') ?>">Daftar sekarang</a></p>
    </div>
</section>