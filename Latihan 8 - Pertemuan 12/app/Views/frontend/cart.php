<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> Belanja ShopEase</span><h1>Keranjang <em>pilihanmu.</em></h1><p>Periksa produk dan jumlah sebelum melanjutkan ke checkout.</p></div>
        <a class="text-link" href="<?= url_to('catalog') ?>">Lanjut belanja <span aria-hidden="true">↗</span></a>
    </div>

    <?php if ($items === []): ?>
        <div class="empty-state"><span class="eyebrow">Belum ada produk</span><h2>Keranjangmu masih kosong.</h2><p>Temukan produk yang kamu suka dan tambahkan ke keranjang.</p><a class="button button-accent" href="<?= url_to('catalog') ?>">Jelajahi produk <span aria-hidden="true">↗</span></a></div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                    <article class="cart-item">
                        <img src="<?= esc(shop_product_image_url($item['image']), 'attr') ?>" alt="<?= esc($item['name']) ?>">
                        <div class="cart-item-copy"><span class="product-category"><?= esc($item['category_name']) ?></span><h2><?= esc($item['name']) ?></h2><p>Rp <?= number_format((float) $item['price'], 0, ',', '.') ?> / produk</p>
                            <?php if (! $item['available']): ?><small class="cart-unavailable">Stok berubah atau produk tidak tersedia. Perbarui jumlah atau hapus produk ini.</small><?php endif; ?>
                        </div>
                        <div class="cart-item-actions">
                            <?php if ($item['status'] === 'active' && (int) $item['stock'] > 0): ?>
                                <form action="<?= url_to('cart.update', $item['id']) ?>" method="post" class="cart-quantity-form">
                                    <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
                                    <label class="visually-hidden" for="quantity-<?= (int) $item['id'] ?>">Jumlah <?= esc($item['name']) ?></label>
                                    <input id="quantity-<?= (int) $item['id'] ?>" type="number" name="quantity" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>" required>
                                    <button class="table-action" type="submit">Perbarui</button>
                                </form>
                            <?php else: ?><span class="cart-unavailable">Jumlah tidak tersedia</span><?php endif; ?>
                            <strong>Rp <?= number_format((float) $item['line_total'], 0, ',', '.') ?></strong>
                            <form action="<?= url_to('cart.remove', $item['id']) ?>" method="post">
                                <?= csrf_field() ?><input type="hidden" name="_method" value="DELETE">
                                <button class="table-action table-action-danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <aside class="cart-summary">
                <span class="eyebrow">Ringkasan</span>
                <div><span>Subtotal produk</span><strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong></div>
                <div><span>Pengiriman</span><strong>Dihitung saat checkout</strong></div>
                <p>Ongkir reguler Rp 15.000 atau ekspres Rp 30.000.</p>
                <?php if ($canCheckout): ?>
                    <a class="button button-accent" href="<?= url_to('checkout') ?>">Lanjut checkout <span aria-hidden="true">↗</span></a>
                    <?php if (! session()->get('logged_in')): ?><small>Masuk dengan akun customer diperlukan untuk checkout.</small><?php endif; ?>
                <?php else: ?>
                    <span class="button button-disabled" aria-disabled="true">Periksa stok terlebih dahulu</span>
                <?php endif; ?>
            </aside>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
