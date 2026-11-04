<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><span class="eyebrow">Administrasi akademik</span><h1>Dashboard</h1><p>Ringkasan data dan kelas aktif SIAKAD.</p></div></div>
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="metric-card"><span>Mahasiswa</span><strong><?= (int) $jumlahMahasiswa ?></strong></div></div>
    <div class="col-6 col-xl-3"><div class="metric-card"><span>Dosen</span><strong><?= (int) $jumlahDosen ?></strong></div></div>
    <div class="col-6 col-xl-3"><div class="metric-card"><span>Mata kuliah</span><strong><?= (int) $jumlahMK ?></strong></div></div>
    <div class="col-6 col-xl-3"><div class="metric-card metric-accent"><span>Kelas terjadwal</span><strong><?= (int) $jumlahKelas ?></strong></div></div>
</div>
<section class="surface-card"><div class="surface-heading"><div><h2>Kelas aktif</h2><p>Pengampu dan kuota kelas yang tersedia.</p></div><a class="btn btn-primary btn-sm" href="<?= site_url('admin/data/kelas') ?>">Kelola kelas</a></div>
<div class="table-responsive"><table class="table data-table mb-0"><thead><tr><th>Mata kuliah</th><th>Kelas</th><th>Pengampu</th><th>Semester</th><th>SKS</th><th>Kuota</th></tr></thead><tbody>
<?php if ($kelas): foreach ($kelas as $row): ?><tr><td><strong><?= esc($row['kode']) ?></strong><span class="d-block small text-muted"><?= esc($row['mata_kuliah']) ?></span></td><td><?= esc($row['nama_kelas']) ?></td><td><?= esc($row['dosen_name']) ?></td><td><?= esc($row['semester']) ?> <?= esc($row['tahun_ajaran']) ?></td><td><?= (int) $row['sks'] ?></td><td><?= (int) $row['kuota'] ?></td></tr><?php endforeach; else: ?><tr><td colspan="6" class="empty-row">Belum ada kelas untuk tahun ajaran <?= esc(date('Y') . '/' . (date('Y') + 1)) ?>.</td></tr><?php endif ?>
</tbody></table></div></section>
<?= $this->endSection() ?>
