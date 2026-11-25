<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="management-page page-width">
    <div class="management-heading">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> Panel admin</span><h1>Kategori <em>produk.</em></h1><p>Atur kelompok produk yang tersedia di katalog ShopEase.</p></div>
        <a class="button button-accent" href="<?= url_to('admin.categories.new') ?>">Tambah kategori <span aria-hidden="true">↗</span></a>
    </div>
    <nav class="management-nav" aria-label="Menu pengelolaan">
        <a href="<?= url_to('admin.products.index') ?>">Produk</a>
        <a class="is-active" href="<?= url_to('admin.categories.index') ?>">Kategori</a>
        <a href="<?= url_to('admin.dashboard') ?>">Dashboard</a>
    </nav>
    <div class="table-scroll">
        <table class="data-table">
            <thead><tr><th>Nama kategori</th><th>Deskripsi singkat</th><th>Penanda</th><th>Produk</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if ($categories === []): ?>
                    <tr><td class="table-empty" colspan="5">Belum ada kategori. Tambahkan kategori sebelum membuat produk.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><strong><?= esc($category['name']) ?></strong></td>
                        <td><?= esc($category['detail']) ?></td>
                        <td><span class="category-mark-badge"><?= esc($category['mark']) ?></span></td>
                        <td><?= (int) $category['product_count'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="table-action" href="<?= url_to('admin.categories.edit', $category['id']) ?>">Ubah</a>
                                <?php if ((int) $category['product_count'] === 0): ?>
                                    <form action="<?= url_to('admin.categories.delete', $category['id']) ?>" method="post" onsubmit="return confirm('Hapus kategori ini?')">
                                        <?= csrf_field() ?><input type="hidden" name="_method" value="DELETE">
                                        <button class="table-action table-action-danger" type="submit">Hapus</button>
                                    </form>
                                <?php else: ?>
                                    <span class="table-action-muted" title="Kategori masih digunakan produk">Dipakai</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
