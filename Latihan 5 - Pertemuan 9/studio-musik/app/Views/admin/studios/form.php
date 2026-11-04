<?php $edit = $studio !== null; ?>
<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?><?= $edit ? 'Ubah studio' : 'Tambah studio' ?><?= $this->endSection() ?>
<?= $this->section('heading') ?><?= $edit ? 'Ubah studio' : 'Tambah studio' ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<form action="<?= site_url($edit ? 'admin/studio/' . (int) $studio['id'] : 'admin/studio') ?>" method="post" enctype="multipart/form-data" class="panel form-narrow" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="name" class="form-label">Nama studio</label>
        <input type="text" class="form-control" id="name" name="name" maxlength="100" value="<?= esc(old('name', $studio['name'] ?? ''), 'attr') ?>" required autofocus>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" id="description" name="description" rows="3" maxlength="500"><?= esc(old('description', $studio['description'] ?? '')) ?></textarea>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label for="hourly_rate" class="form-label">Tarif per jam (Rp)</label>
            <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" min="1" step="1000" value="<?= esc(old('hourly_rate', $studio['hourly_rate'] ?? ''), 'attr') ?>" required>
        </div>
        <div class="col-sm-6">
            <label for="capacity" class="form-label">Kapasitas (orang)</label>
            <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="200" value="<?= esc(old('capacity', $studio['capacity'] ?? ''), 'attr') ?>" required>
        </div>
    </div>
    <div class="mb-3">
        <label for="photo" class="form-label">Foto studio (JPG, PNG, atau WebP, maksimal 2 MB)</label>
        <?php if ($edit && $studio['photo']): ?>
            <div class="mb-2"><img src="<?= base_url('uploads/studios/' . esc($studio['photo'], 'url')) ?>" alt="Foto saat ini" class="thumb thumb-lg"></div>
        <?php endif; ?>
        <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp">
        <?php if ($edit): ?><div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div><?php endif; ?>
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= old('name') !== null ? (old('is_active') ? 'checked' : '') : (! $edit || $studio['is_active'] ? 'checked' : '') ?>>
        <label class="form-check-label" for="is_active">Studio aktif dan dapat dibooking</label>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-outline-dark" href="<?= site_url('admin/studio') ?>">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
