<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$isEditing = is_array($category);
$value = static fn (string $key, mixed $default = '') => $values[$key] ?? ($category[$key] ?? $default);
$action = $isEditing ? url_to('admin.categories.update', $category['id']) : url_to('admin.categories.create');
?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact">
        <div><span class="eyebrow"><span class="eyebrow-dot"></span> Panel admin / Kategori</span><h1><?= $isEditing ? 'Ubah' : 'Tambah' ?> <em>kategori.</em></h1><p>Kategori membantu pembeli menjelajahi produk berdasarkan kebutuhan.</p></div>
        <a class="text-link" href="<?= url_to('admin.categories.index') ?>">Kembali ke daftar <span aria-hidden="true">↗</span></a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="form-alert form-error" role="alert"><strong>Periksa kembali data kategori.</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form class="management-form category-form" action="<?= $action ?>" method="post">
        <?= csrf_field() ?>
        <?php if ($isEditing): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
        <label>Nama kategori
            <input type="text" name="name" maxlength="100" required value="<?= esc($value('name'), 'attr') ?>" placeholder="Contoh: Teknologi">
        </label>
        <label>Deskripsi singkat
            <input type="text" name="detail" maxlength="180" required value="<?= esc($value('detail'), 'attr') ?>" placeholder="Teman produktif sehari-hari">
        </label>
        <label>Penanda di beranda
            <input type="text" name="mark" maxlength="5" required value="<?= esc($value('mark'), 'attr') ?>" placeholder="01">
            <small>Gunakan angka pendek agar muat di kartu kategori.</small>
        </label>
        <div class="management-form-actions">
            <a class="button button-outline" href="<?= url_to('admin.categories.index') ?>">Batal</a>
            <button class="button button-accent" type="submit"><?= $isEditing ? 'Simpan perubahan' : 'Simpan kategori' ?> <span aria-hidden="true">↗</span></button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
