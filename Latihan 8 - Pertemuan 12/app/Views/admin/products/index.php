<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="management-page page-width">
    <div class="management-heading">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> Panel admin</span><h1>Produk <em>ShopEase.</em></h1><p>Kelola informasi, harga, stok, gambar, dan status produk toko.</p></div>
        <a class="button button-accent" href="<?= url_to('admin.products.new') ?>">Tambah produk <span aria-hidden="true">↗</span></a>
    </div>
    <nav class="management-nav" aria-label="Menu pengelolaan">
        <a class="is-active" href="<?= url_to('admin.products.index') ?>">Produk</a>
        <a href="<?= url_to('admin.categories.index') ?>">Kategori</a>
        <a href="<?= url_to('admin.dashboard') ?>">Dashboard</a>
    </nav>
    <div class="management-summary"><span><?= count($products) ?> produk di halaman ini</span><span>Gunakan tombol ubah atau hapus untuk mengelola data.</span></div>
    <div class="table-scroll">
        <table class="data-table">
            <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if ($products === []): ?>
                    <tr><td class="table-empty" colspan="6">Belum ada produk. Tambahkan produk pertama untuk mulai mengisi katalog.</td></tr>
                <?php endif; ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><div class="table-product"><img src="<?= esc(shop_product_image_url($product['image']), 'attr') ?>" alt=""><div><strong><?= esc($product['name']) ?></strong><small><?= esc(mb_strimwidth($product['description'], 0, 78, '…')) ?></small></div></div></td>
                        <td><?= esc($product['category_name']) ?></td>
                        <td>Rp <?= number_format((float) $product['price'], 0, ',', '.') ?></td>
                        <td><?= (int) $product['stock'] ?></td>
                        <td><span class="status-pill <?= $product['status'] === 'active' ? 'status-active' : 'status-inactive' ?>"><?= $product['status'] === 'active' ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a class="table-action" href="<?= url_to('admin.products.edit', $product['id']) ?>">Ubah</a>
                                <form action="<?= url_to('admin.products.delete', $product['id']) ?>" method="post" onsubmit="return confirm('Hapus produk ini?')">
                                    <?= csrf_field() ?><input type="hidden" name="_method" value="DELETE">
                                    <button class="table-action table-action-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links() ?>
</section>
<?= $this->endSection() ?>
