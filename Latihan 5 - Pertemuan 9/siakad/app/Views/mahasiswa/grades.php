<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$groups = []; $totalPoints = 0; $totalSks = 0;
$points = ['A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0];
foreach ($grades as $item) { $key = $item['semester'] . ' · ' . $item['tahun_ajaran']; $groups[$key][] = $item; $point = $points[$item['nilai_huruf']] ?? 0; $totalPoints += $point * (int) $item['sks']; $totalSks += (int) $item['sks']; }
?>
<div class="page-heading"><div><span class="eyebrow">Hasil studi</span><h1>Nilai dan IPK</h1><p>Rekapitulasi nilai mata kuliah yang telah diselesaikan.</p></div><?php if ($grades): ?><a class="btn btn-primary" href="<?= site_url('mahasiswa/transkrip/cetak') ?>" target="_blank">Cetak transkrip</a><?php endif ?></div>
<section class="gpa-overview"><div><span>IPK kumulatif</span><strong><?= $totalSks ? number_format($totalPoints / $totalSks, 2) : '—' ?></strong></div><div><span>Total SKS lulus / dinilai</span><strong><?= $totalSks ?> <small>SKS</small></strong></div><div class="gpa-note">IPK dihitung dari nilai huruf dengan bobot mutu dan SKS mata kuliah.</div></section>
<?php if (! $groups): ?><div class="empty-state">Belum ada nilai yang diterbitkan dosen.</div><?php else: foreach ($groups as $term => $items): $termPoints = 0; $termSks = 0; foreach ($items as $item) { $termPoints += ($points[$item['nilai_huruf']] ?? 0) * (int) $item['sks']; $termSks += (int) $item['sks']; } ?>
<section class="surface-card term-card"><div class="surface-heading"><div><h2><?= esc($term) ?></h2><p><?= count($items) ?> mata kuliah · <?= $termSks ?> SKS</p></div><div class="term-gpa"><span>IPS</span><strong><?= $termSks ? number_format($termPoints / $termSks, 2) : '—' ?></strong></div></div><div class="table-responsive"><table class="table data-table mb-0"><thead><tr><th>Kode</th><th>Mata kuliah</th><th>SKS</th><th>Nilai angka</th><th>Nilai huruf</th></tr></thead><tbody><?php foreach ($items as $item): ?><tr><td><?= esc($item['kode']) ?></td><td><strong><?= esc($item['mata_kuliah']) ?></strong></td><td><?= (int) $item['sks'] ?></td><td><?= number_format((float) $item['nilai_angka'], 2) ?></td><td><span class="grade-pill"><?= esc($item['nilai_huruf']) ?></span></td></tr><?php endforeach ?></tbody></table></div></section>
<?php endforeach; endif ?>
<?= $this->endSection() ?>
