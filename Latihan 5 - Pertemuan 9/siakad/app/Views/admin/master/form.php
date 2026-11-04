<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><span class="eyebrow">Data akademik</span><h1><?= esc($title) ?></h1><p>Lengkapi informasi di bawah ini.</p></div></div>
<section class="surface-card form-card"><form method="post" action="<?= site_url('admin/data/' . $entity . ($record ? '/' . (int) $record['id'] : '')) ?>"><?= csrf_field() ?>
<div class="row g-3">
<?php foreach ($config['fields'] as $field): $key = $field['key']; $value = old($key, $record[$key] ?? ''); ?>
<div class="col-md-<?= in_array($key, ['name', 'email', 'nama', 'tahun_ajaran'], true) ? '6' : '6' ?>"><label class="form-label" for="field-<?= esc($key) ?>"><?= esc($field['label']) ?></label>
<?php if ($field['type'] === 'prodi'): ?>
    <select class="form-select" id="field-<?= esc($key) ?>" name="<?= esc($key) ?>" <?= ($field['optional'] ?? false) ? '' : 'required' ?>><option value="">Pilih program studi</option><?php foreach ($options['prodi'] as $option): ?><option value="<?= (int) $option['id'] ?>" <?= (string) $value === (string) $option['id'] ? 'selected' : '' ?>><?= esc($option['kode'] . ' — ' . $option['nama']) ?></option><?php endforeach ?></select>
<?php elseif ($field['type'] === 'dosen'): ?>
    <select class="form-select" id="field-<?= esc($key) ?>" name="<?= esc($key) ?>" <?= ($field['optional'] ?? false) ? '' : 'required' ?>><option value="">Pilih dosen<?= ($field['optional'] ?? false) ? ' (opsional)' : '' ?></option><?php foreach ($options['dosen'] as $option): ?><option value="<?= (int) $option['id'] ?>" <?= (string) $value === (string) $option['id'] ? 'selected' : '' ?>><?= esc($option['name'] . ' — ' . $option['email']) ?></option><?php endforeach ?></select>
<?php elseif ($field['type'] === 'matakuliah'): ?>
    <select class="form-select" id="field-<?= esc($key) ?>" name="<?= esc($key) ?>" required><option value="">Pilih mata kuliah</option><?php foreach ($options['matakuliah'] as $option): ?><option value="<?= (int) $option['id'] ?>" <?= (string) $value === (string) $option['id'] ? 'selected' : '' ?>><?= esc($option['kode'] . ' — ' . $option['nama']) ?></option><?php endforeach ?></select>
<?php elseif ($field['type'] === 'semester'): ?>
    <select class="form-select" id="field-<?= esc($key) ?>" name="<?= esc($key) ?>" required><option value="">Pilih semester</option><?php foreach (['Ganjil', 'Genap'] as $option): ?><option <?= $value === $option ? 'selected' : '' ?>><?= esc($option) ?></option><?php endforeach ?></select>
<?php else: ?>
    <input class="form-control" id="field-<?= esc($key) ?>" type="<?= esc($field['type']) ?>" name="<?= esc($key) ?>" value="<?= esc((string) $value, 'attr') ?>" <?= $field['type'] === 'number' ? 'min="1"' : '' ?> required>
<?php endif ?>
<?php if ($error = session()->getFlashdata('errors')[$key] ?? null): ?><div class="invalid-feedback d-block"><?= esc($error) ?></div><?php endif ?>
<?php if (in_array($entity, ['mahasiswa', 'dosen'], true) && $key === 'email' && ! $record): ?><div class="form-text">Kata sandi awal: <?= $entity === 'dosen' ? 'Dosen123!' : 'Mahasiswa123!' ?>. Minta pengguna menggantinya setelah masuk.</div><?php endif ?>
</div>
<?php endforeach ?>
</div><div class="form-actions"><a class="btn btn-light border" href="<?= site_url('admin/data/' . $entity) ?>">Batal</a><button class="btn btn-primary" type="submit">Simpan</button></div></form></section>
<?= $this->endSection() ?>
