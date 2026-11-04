<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><span class="eyebrow">Pembimbing akademik</span><h1>Mahasiswa Perwalian</h1><p>Daftar mahasiswa yang berada dalam bimbingan akademik Anda.</p></div></div>
<section class="surface-card"><div class="table-responsive"><table class="table data-table mb-0"><thead><tr><th>NIM</th><th>Nama</th><th>Program studi</th><th>Angkatan</th><th>Email</th></tr></thead><tbody><?php if ($mahasiswa): foreach ($mahasiswa as $row): ?><tr><td><?= esc($row['nim']) ?></td><td><strong><?= esc($row['name']) ?></strong></td><td><?= esc($row['prodi_name']) ?></td><td><?= (int) $row['angkatan'] ?></td><td><?= esc($row['email']) ?></td></tr><?php endforeach; else: ?><tr><td colspan="5" class="empty-row">Belum ada mahasiswa perwalian.</td></tr><?php endif ?></tbody></table></div></section>
<?= $this->endSection() ?>
