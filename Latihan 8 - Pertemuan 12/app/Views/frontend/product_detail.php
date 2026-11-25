<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="product-detail page-width">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= url_to('home') ?>">Beranda</a><span aria-hidden="true">/</span><a href="<?= url_to('catalog') ?>">Katalog</a><span aria-hidden="true">/</span><span><?= esc($product['name']) ?></span>
    </nav>
    <div class="product-detail-grid">
        <div class="product-detail-image"><img src="<?= esc(shop_product_image_url($product['image']), 'attr') ?>" alt="<?= esc($product['name']) ?>"></div>
        <div class="product-detail-copy">
            <span class="eyebrow"><span class="eyebrow-dot"></span><?= esc($product['category_name']) ?></span>
            <h1><?= esc($product['name']) ?>.</h1>
            <p class="product-detail-price">Rp <?= number_format((float) $product['price'], 0, ',', '.') ?></p>
            <p class="product-detail-description"><?= nl2br(esc($product['description'])) ?></p>
            <div class="product-stock"><span class="stock-mark"></span><?= (int) $product['stock'] > 0 ? 'Stok tersedia: ' . (int) $product['stock'] : 'Stok sedang habis' ?></div>
            <?php if ((int) $product['stock'] > 0): ?>
                <form class="add-cart-form" action="<?= url_to('cart.add') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <label>Jumlah <input type="number" name="quantity" min="1" max="<?= (int) $product['stock'] ?>" value="1" required></label>
                    <button class="button button-accent" type="submit">Tambah ke keranjang <span aria-hidden="true">↗</span></button>
                </form>
            <?php else: ?>
                <p class="stock-unavailable">Produk sedang habis.</p>
            <?php endif; ?>
            <a class="text-link product-back-link" href="<?= url_to('catalog') ?>">Kembali ke katalog <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
