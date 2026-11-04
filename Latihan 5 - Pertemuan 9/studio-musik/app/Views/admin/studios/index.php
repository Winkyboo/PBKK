<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Studio<?= $this->endSection() ?>
<?= $this->section('heading') ?>Studio<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="mb-3"><a class="btn btn-primary" href="<?= site_url('admin/studio/baru') ?>">Tambah studio</a></div>
<div class="panel">
<?php if (! $studios): ?>
    <p class="text-secondary mb-0">Belum ada studio. Klik Tambah studio untuk membuat yang pertama.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table studio-table align-middle mb-0">
            <thead><tr><th scope="col">Foto</th><th scope="col">Nama</th><th scope="col">Tarif per jam</th><th scope="col">Kapasitas</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($studios as $s): ?>
                <tr>
                    <td>
                        <?php if ($s['photo']): ?>
                            <img src="<?= base_url('uploads/studios/' . esc($s['photo'], 'url')) ?>" alt="Foto <?= esc($s['name'], 'attr') ?>" class="thumb">
                        <?php else: ?>
                            <span class="text-secondary small">Belum ada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($s['name']) ?></td>
                    <td><?= rupiah($s['hourly_rate']) ?></td>
                    <td><?= (int) $s['capacity'] ?> orang</td>
                    <td><?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?></td>
                    <td class="d-flex gap-2">
                        <a class="btn btn-outline-dark btn-sm" href="<?= site_url('admin/studio/' . (int) $s['id'] . '/ubah') ?>">Ubah</a>
                        <form action="<?= site_url('admin/studio/' . (int) $s['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus studio ini? Tindakan ini tidak dapat dibatalkan.');">
                            <?= csrf_field() ?>
                            <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>
<?= $this->endSection() ?>
