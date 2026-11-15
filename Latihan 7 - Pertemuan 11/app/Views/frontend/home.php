<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="hero page-width">
    <div class="hero-copy">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Pilihan kecil, hari lebih baik</span>
        <h1>Belanja mudah,<br>hidup lebih <em>ringan.</em></h1>
        <p>Temukan barang pilihan yang membuat rutinitas terasa lebih nyaman. Kurasi sederhana, pengalaman belanja yang menyenangkan.</p>
        <div class="hero-actions">
            <a class="button button-accent" href="#koleksi">Temukan pilihanmu <span aria-hidden="true">↗</span></a>
            <a class="text-link" href="<?= url_to('about') ?>">Kenali ShopEase <span aria-hidden="true">→</span></a>
        </div>
        <div class="hero-proof"><span class="proof-mark" aria-hidden="true">✓</span><span>Pilihan tepercaya untuk keseharianmu</span></div>
    </div>
    <div class="hero-visual" aria-label="Ilustrasi tas belanja dan produk pilihan" role="img">
        <span class="visual-orbit orbit-one"></span><span class="visual-orbit orbit-two"></span>
        <span class="visual-caption caption-top">PILIHAN BAIK, SETIAP HARI</span>
        <img src="<?= base_url('assets/img/shop-hero.svg') ?>" alt="Ilustrasi tas belanja ShopEase dengan tumbler, buku, dan tanaman">
        <div class="floating-note"><span class="note-mark" aria-hidden="true">●</span><span><strong>Dipilih dengan cermat</strong><small>Untuk hal yang benar-benar berarti</small></span></div>
        <span class="visual-caption caption-bottom">KURASI HARIAN&nbsp; · &nbsp;NO. 01</span>
    </div>
</section>

<section class="category-section page-width" id="koleksi">
    <div class="section-heading">
        <div><span class="eyebrow">Jelajahi yang kamu suka</span><h2>Ruang untuk pilihanmu.</h2></div>
        <p>Hal-hal esensial, dikurasi supaya harimu terasa lebih baik.</p>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $category): ?>
            <a class="category-card" href="#produk">
                <span class="category-mark"><?= esc($category['mark']) ?></span>
                <span class="category-copy"><strong><?= esc($category['name']) ?></strong><small><?= esc($category['detail']) ?></small></span>
                <span class="category-arrow" aria-hidden="true">↗</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="product-section page-width" id="produk">
    <div class="section-heading product-heading">
        <div><span class="eyebrow">Pilihan minggu ini</span><h2>Favorit yang sederhana.</h2></div>
        <span class="product-count">01 — 03</span>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $index => $product): ?>
            <article class="product-card">
                <div class="product-art <?= esc($product['tone'], 'attr') ?>">
                    <?php $productImage = is_file(FCPATH . $product['image']) ? $product['image'] : $product['fallback']; ?>
                    <span class="product-index">0<?= $index + 1 ?></span><img class="product-art-image" src="<?= base_url($productImage) ?>" alt="">
                    <span class="product-tag">Pilihan ShopEase</span>
                </div>
                <div class="product-meta"><div><span class="product-category"><?= esc($product['category']) ?></span><h3><?= esc($product['name']) ?></h3></div><span class="product-arrow" aria-hidden="true">↗</span></div>
                <p class="product-price">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="closing-note page-width"><span class="closing-mark" aria-hidden="true"></span><p>Lebih sedikit mencari.<br><em>Lebih banyak menikmati.</em></p><a href="<?= url_to('about') ?>">Cerita kami <span aria-hidden="true">→</span></a></section>
<?= $this->endSection() ?>
