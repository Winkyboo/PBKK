<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Alat sewa<?= $this->endSection() ?>
<?= $this->section('heading') ?>Alat sewa<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="mb-3"><a class="btn btn-primary" href="<?= site_url('admin/alat/baru') ?>">Tambah alat</a></div>
<div class="panel">
<?php if (! $equipments): ?>
    <p class="text-secondary mb-0">Belum ada alat. Klik Tambah alat untuk membuat yang pertama.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table studio-table align-middle mb-0">
            <thead><tr><th scope="col">Nama</th><th scope="col">Kategori</th><th scope="col" class="text-end">Stok</th><th scope="col" class="text-end">Harga sewa</th><th scope="col">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($equipments as $e): ?>
                <tr>
                    <td><?= esc($e['name']) ?></td>
                    <td><?= esc($e['category']) ?></td>
                    <td class="text-end"><?= (int) $e['stock'] ?></td>
                    <td class="text-end"><?= rupiah($e['rental_price']) ?></td>
                    <td class="d-flex gap-2">
                        <a class="btn btn-outline-dark btn-sm" href="<?= site_url('admin/alat/' . (int) $e['id'] . '/ubah') ?>">Ubah</a>
                        <form action="<?= site_url('admin/alat/' . (int) $e['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus alat ini?');">
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
