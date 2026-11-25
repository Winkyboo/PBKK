<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-intro page-width">
    <span class="eyebrow"><span class="eyebrow-dot"></span> Katalog ShopEase</span>
    <h1>Temukan <em>pilihanmu.</em></h1>
    <p>Telusuri produk pilihan, cari nama yang kamu inginkan, dan saring berdasarkan kategori.</p>
</section>

<section class="catalog-section page-width">
    <form class="catalog-filters" action="<?= url_to('catalog') ?>" method="get">
        <label class="catalog-search">
            <span>Cari produk</span>
            <input type="search" name="q" value="<?= esc($filters['q']) ?>" placeholder="Nama atau deskripsi produk">
        </label>
        <label>
            <span>Kategori</span>
            <select name="category">
                <option value="0">Semua kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) $filters['category'] === (int) $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Urutkan harga</span>
            <select name="sort">
                <option value="">Terbaru</option>
                <option value="price-low" <?= $filters['sort'] === 'price-low' ? 'selected' : '' ?>>Termurah</option>
                <option value="price-high" <?= $filters['sort'] === 'price-high' ? 'selected' : '' ?>>Termahal</option>
            </select>
        </label>
        <button class="button button-accent" type="submit">Terapkan <span aria-hidden="true">↗</span></button>
    </form>

    <?php if ($products === []): ?>
        <div class="empty-state"><span class="eyebrow">Belum ada hasil</span><h2>Produk tidak ditemukan.</h2><p>Coba ubah kata pencarian atau pilih kategori lain.</p><a class="text-link" href="<?= url_to('catalog') ?>">Hapus filter <span aria-hidden="true">↗</span></a></div>
    <?php else: ?>
        <div class="product-grid catalog-grid">
            <?php foreach ($products as $index => $product): ?>
                <?php $tone = ['product-sand', 'product-peach', 'product-olive'][$index % 3]; ?>
                <article class="product-card">
                    <a class="product-art <?= esc($tone, 'attr') ?>" href="<?= url_to('product.show', $product['id']) ?>">
                        <span class="product-index"><?= esc(str_pad((string) $product['id'], 2, '0', STR_PAD_LEFT)) ?></span>
                        <img class="product-art-image" src="<?= esc(shop_product_image_url($product['image']), 'attr') ?>" alt="<?= esc($product['name']) ?>">
                        <span class="product-tag">Tersedia <?= (int) $product['stock'] ?></span>
                    </a>
                    <div class="product-meta"><div><span class="product-category"><?= esc($product['category_name']) ?></span><h3><a href="<?= url_to('product.show', $product['id']) ?>"><?= esc($product['name']) ?></a></h3></div><a class="product-arrow" href="<?= url_to('product.show', $product['id']) ?>" aria-label="Lihat <?= esc($product['name']) ?>">↗</a></div>
                    <p class="product-price">Rp <?= number_format((float) $product['price'], 0, ',', '.') ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <?= $pager->links() ?>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
