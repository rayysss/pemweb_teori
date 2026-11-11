<section class="container standard-page">
    <div class="page-title">
        <span class="eyebrow">LANGKAH TERAKHIR</span>
        <h1>Checkout</h1>
        <p>Lengkapi informasi pengiriman dan pilih metode pembayaran.</p>
    </div>

    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $err): ?>
                <div><?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="checkout-layout">
        <form class="form-card" method="post" action="<?= base_url('checkout') ?>">
            <?= csrf_field() ?>
            
            <h3>Informasi penerima</h3>
            <div class="form-grid">
                <label>Nama penerima
                    <input name="recipient_name" required value="<?= old('recipient_name',current_user()['name']) ?>">
                </label>
                <label>Nomor telepon
                    <input name="recipient_phone" required value="<?= old('recipient_phone',current_user()['phone']??'') ?>">
                </label>
                <label class="full">Alamat lengkap
                    <textarea name="address" rows="3" required><?= old('address') ?></textarea>
                </label>
                <label>Kota
                    <input name="city" required value="<?= old('city') ?>">
                </label>
                <label>Provinsi
                    <input name="province" value="<?= old('province') ?>">
                </label>
                <label>Kode pos
                    <input name="postal_code" value="<?= old('postal_code') ?>">
                </label>
                <label class="full">Catatan (opsional)
                    <textarea name="note" rows="2"><?= old('note') ?></textarea>
                </label>
            </div>

            <h3 class="form-heading-spaced">Metode pembayaran</h3>
            <div class="payment-options">
                <label>
                    <input type="radio" name="payment_method" value="bank_transfer" checked>
                    <span>
                        <strong>Transfer bank</strong>
                        <small>Konfirmasi pembayaran manual</small>
                    </span>
                </label>
                <label>
                    <input type="radio" name="payment_method" value="qris">
                    <span>
                        <strong>QRIS</strong>
                        <small>Pembayaran QRIS (simulasi)</small>
                    </span>
                </label>
                <label>
                    <input type="radio" name="payment_method" value="ewallet">
                    <span>
                        <strong>E-wallet</strong>
                        <small>Pembayaran dompet digital (simulasi)</small>
                    </span>
                </label>
                <label>
                    <input type="radio" name="payment_method" value="cod">
                    <span>
                        <strong>Bayar di tempat</strong>
                        <small>Cash on delivery</small>
                    </span>
                </label>
            </div>

            <button class="btn-zora w-100">Buat pesanan →</button>
        </form>

        <aside class="summary-card">
            <h3>Pesananmu</h3>
            <?php foreach($items as $item): ?>
                <div class="summary-product">
                    <span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span>
                    <strong><?= rupiah($item['unit_price']*$item['quantity']) ?></strong>
                </div>
            <?php endforeach; ?>
            
            <div>
                <span>Subtotal</span>
                <strong><?= rupiah($subtotal) ?></strong>
            </div>
            <div>
                <span>Pengiriman</span>
                <strong><?= $shippingCost===0?'Gratis':rupiah($shippingCost) ?></strong>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <strong><?= rupiah($subtotal+$shippingCost) ?></strong>
            </div>
        </aside>
    </div>
</section>