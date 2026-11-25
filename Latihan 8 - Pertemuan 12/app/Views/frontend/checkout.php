<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $value = static fn (string $key, string $default = ''): string => (string) ($values[$key] ?? $default); ?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> Langkah terakhir</span><h1>Checkout <em>pesanan.</em></h1><p>Isi alamat tujuan dan pilih layanan pengiriman.</p></div>
        <a class="text-link" href="<?= url_to('cart') ?>">Kembali ke keranjang <span aria-hidden="true">↗</span></a>
    </div>

    <?php if ($errors !== []): ?><div class="form-alert form-error" role="alert"><strong>Periksa kembali informasi checkout.</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

    <div class="checkout-layout">
        <form class="management-form checkout-form" action="<?= url_to('checkout.place') ?>" method="post">
            <?= csrf_field() ?>
            <div class="checkout-section"><span class="eyebrow">01 / Penerima</span><div class="management-form-grid">
                <label>Nama penerima<input type="text" name="recipient_name" maxlength="100" required value="<?= esc($value('recipient_name', (string) session()->get('user_name')), 'attr') ?>"></label>
                <label>Nomor telepon<input type="tel" name="phone" maxlength="20" required value="<?= esc($value('phone'), 'attr') ?>" placeholder="08xxxxxxxxxx"></label>
                <label class="form-field-wide">Alamat lengkap<textarea name="shipping_address" maxlength="500" required placeholder="Nama jalan, nomor rumah, RT/RW, patokan"><?= esc($value('shipping_address')) ?></textarea></label>
                <label>Kota / Kabupaten<input type="text" name="city" maxlength="100" required value="<?= esc($value('city'), 'attr') ?>"></label>
                <label>Kode pos<input type="text" name="postal_code" maxlength="10" required value="<?= esc($value('postal_code'), 'attr') ?>"></label>
            </div></div>

            <div class="checkout-section"><span class="eyebrow">02 / Pengiriman</span><div class="shipping-options">
                <label class="shipping-option"><input type="radio" name="shipping_method" value="regular" <?= $value('shipping_method', 'regular') === 'regular' ? 'checked' : '' ?>><span><strong>Reguler</strong><small>Estimasi 3–5 hari kerja</small></span><b>Rp 15.000</b></label>
                <label class="shipping-option"><input type="radio" name="shipping_method" value="express" <?= $value('shipping_method') === 'express' ? 'checked' : '' ?>><span><strong>Ekspres</strong><small>Estimasi 1–2 hari kerja</small></span><b>Rp 30.000</b></label>
            </div></div>

            <div class="checkout-section"><span class="eyebrow">03 / Pembayaran</span><div class="payment-note"><strong>Bayar di tempat (COD)</strong><p>Pembayaran dilakukan kepada kurir saat pesanan diterima. Admin akan memperbarui status pembayaran.</p><input type="hidden" name="payment_method" value="cod"></div></div>

            <div class="management-form-actions"><button class="button button-accent" type="submit">Buat pesanan <span aria-hidden="true">↗</span></button></div>
        </form>

        <aside class="cart-summary checkout-summary"><span class="eyebrow">Pesananmu</span>
            <?php foreach ($items as $item): ?><div class="checkout-product"><span><?= esc($item['name']) ?> × <?= (int) $item['quantity'] ?></span><strong>Rp <?= number_format((float) $item['line_total'], 0, ',', '.') ?></strong></div><?php endforeach; ?>
            <div><span>Subtotal</span><strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></div>
            <div class="shipping-estimate"><span>Pengiriman</span><strong>Pilih layanan</strong></div>
            <p>Total akhir mencakup ongkir yang dipilih dan ditampilkan di detail pesanan.</p>
        </aside>
    </div>
</section>
<?= $this->endSection() ?>
