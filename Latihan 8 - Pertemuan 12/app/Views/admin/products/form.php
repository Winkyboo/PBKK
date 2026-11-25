<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$isEditing = is_array($product);
$value = static fn (string $key, mixed $default = '') => $values[$key] ?? ($product[$key] ?? $default);
$action = $isEditing ? url_to('admin.products.update', $product['id']) : url_to('admin.products.create');
?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> Panel admin / Produk</span><h1><?= $isEditing ? 'Ubah' : 'Tambah' ?> <em>produk.</em></h1><p>Lengkapi informasi produk yang akan tampil di katalog ShopEase.</p></div>
        <a class="text-link" href="<?= url_to('admin.products.index') ?>">Kembali ke daftar <span aria-hidden="true">↗</span></a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="form-alert form-error" role="alert"><strong>Periksa kembali data produk.</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form class="management-form" action="<?= $action ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if ($isEditing): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
        <div class="management-form-grid">
            <label>Nama produk
                <input type="text" name="name" maxlength="120" required value="<?= esc($value('name'), 'attr') ?>" placeholder="Contoh: Headphone Studio">
            </label>
            <label>Kategori
                <select name="category_id" required>
                    <option value="">Pilih kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= (string) $value('category_id') === (string) $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Harga (Rupiah)
                <input type="number" name="price" min="1" step="1" required value="<?= esc($value('price'), 'attr') ?>" placeholder="250000">
            </label>
            <label>Stok
                <input type="number" name="stock" min="0" step="1" required value="<?= esc($value('stock', 0), 'attr') ?>" placeholder="10">
            </label>
            <label class="form-field-wide">Deskripsi
                <textarea name="description" rows="5" maxlength="1000" required placeholder="Jelaskan produk secara singkat dan jelas."><?= esc($value('description')) ?></textarea>
            </label>
            <label>Status
                <?php $status = (string) $value('status', 'active'); ?>
                <select name="status" required>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif — tampil di katalog</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Nonaktif — disembunyikan</option>
                </select>
            </label>
            <label>Foto produk <?= $isEditing ? '(opsional, biarkan kosong untuk memakai foto saat ini)' : '' ?>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" <?= $isEditing ? '' : 'required' ?>>
                <small>Format JPG, PNG, atau WEBP; ukuran maksimal 2 MB.</small>
            </label>
            <?php if ($isEditing): ?>
                <div class="current-product-image"><span>Foto saat ini</span><img src="<?= esc(shop_product_image_url($product['image']), 'attr') ?>" alt="<?= esc($product['name']) ?>"></div>
            <?php endif; ?>
        </div>
        <div class="management-form-actions">
            <a class="button button-outline" href="<?= url_to('admin.products.index') ?>">Batal</a>
            <button class="button button-accent" type="submit"><?= $isEditing ? 'Simpan perubahan' : 'Simpan produk' ?> <span aria-hidden="true">↗</span></button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
