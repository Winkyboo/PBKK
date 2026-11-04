<?php $edit = $equipment !== null; ?>
<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= $edit ? 'Ubah alat' : 'Tambah alat' ?><?= $this->endSection() ?>
<?= $this->section('heading') ?><?= $edit ? 'Ubah alat' : 'Tambah alat' ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<form action="<?= site_url($edit ? 'admin/alat/' . (int) $equipment['id'] : 'admin/alat') ?>" method="post" class="panel form-narrow" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Nama alat</label>
        <input type="text" class="form-control" id="name" name="name" maxlength="100" value="<?= esc(old('name', $equipment['name'] ?? ''), 'attr') ?>" required autofocus>
    </div>
    <div class="mb-3">
        <label for="category" class="form-label">Kategori</label>
        <input type="text" class="form-control" id="category" name="category" maxlength="50" value="<?= esc(old('category', $equipment['category'] ?? ''), 'attr') ?>" required>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <label for="stock" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?= esc(old('stock', $equipment['stock'] ?? '0'), 'attr') ?>" required>
        </div>
        <div class="col-sm-6">
            <label for="rental_price" class="form-label">Harga sewa per sesi (Rp)</label>
            <input type="number" class="form-control" id="rental_price" name="rental_price" min="0" step="1000" value="<?= esc(old('rental_price', $equipment['rental_price'] ?? '0'), 'attr') ?>" required>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-outline-dark" href="<?= site_url('admin/alat') ?>">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
