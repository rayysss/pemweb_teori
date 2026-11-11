<section class="container auth-page">
    <div class="auth-card">
        <span class="eyebrow">GABUNG DENGAN ZORA</span>
        <h1>Buat akun</h1>
        <p>Daftar untuk menyimpan detail pesanan dan berbelanja lebih mudah.</p>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $err): ?>
                    <div><?= e($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= base_url('register') ?>">
            <?= csrf_field() ?>
            <label>Nama lengkap
                <input name="name" required value="<?= old('name') ?>">
            </label>
            <label>Email
                <input type="email" name="email" required value="<?= old('email') ?>">
            </label>
            <label>Nomor telepon (opsional)
                <input name="phone" value="<?= old('phone') ?>">
            </label>
            <label>Password
                <input type="password" name="password" minlength="8" required>
            </label>
            <label>Konfirmasi password
                <input type="password" name="password_confirmation" minlength="8" required>
            </label>
            <button class="btn-zora w-100">Buat akun →</button>
        </form>
        
        <p class="auth-switch">Sudah punya akun? <a href="<?= base_url('login') ?>">Masuk</a></p>
    </div>
</section>