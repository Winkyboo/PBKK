<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?>Beranda<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?php $featured = $studios[0] ?? null; ?>
<section class="hero" aria-labelledby="hero-title">
    <div class="row g-3 g-lg-4 align-items-center">
        <div class="col-lg-6">
            <div class="hero-copy">
                <p class="hero-kicker">Studio musik &amp; ruang rekaman</p>
                <h1 id="hero-title" class="hand hero-title">Temukan ruang untuk suaramu.</h1>
                <p class="hero-lead">Pesan studio latihan dan rekaman per jam. Cek jadwal, pilih ruang yang cocok, lalu kelola booking dengan mudah.</p>
                <div class="hero-actions">
                    <?php if (has_role('pelanggan')): ?>
                        <a class="btn btn-primary px-4 py-2" href="<?= site_url('pelanggan/booking/baru') ?>">Ajukan booking</a>
                    <?php elseif (has_role('admin')): ?>
                        <a class="btn btn-primary px-4 py-2" href="<?= site_url('admin') ?>">Buka panel admin</a>
                    <?php else: ?>
                        <a class="btn btn-primary px-4 py-2" href="<?= site_url('daftar') ?>">Buat akun</a>
                        <a class="btn btn-outline-dark px-4 py-2" href="<?= site_url('login') ?>">Masuk</a>
                    <?php endif; ?>
                    <a class="btn btn-light border px-4 py-2" href="#pilihan-studio">Jelajahi studio</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="hero-visual">
                <?php if ($featured && $featured['photo']): ?>
                    <img class="hero-image" src="<?= base_url('uploads/studios/' . esc($featured['photo'], 'url')) ?>" alt="Ruang <?= esc($featured['name'], 'attr') ?>">
                    <div class="hero-caption">
                        <div><small>Studio pilihan</small><strong><?= esc($featured['name']) ?></strong></div>
                        <div class="hero-caption-rate"><?= rupiah($featured['hourly_rate']) ?><small>/ jam</small></div>
                    </div>
                <?php else: ?>
                    <div class="hero-image-placeholder">
                        <strong>Ruang untuk setiap sesi.</strong>
                        <span>Pilih studio latihan atau rekaman sesuai kebutuhanmu.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section id="pilihan-studio" class="home-section" aria-labelledby="studio-heading">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Pilih ruangmu</span>
            <h2 id="studio-heading" class="section-title">Pilihan studio</h2>
            <p>Ruang latihan dan rekaman dengan tarif yang jelas, siap menemani sesi berikutnya.</p>
        </div>
        <?php if (has_role('pelanggan')): ?>
            <a class="btn btn-outline-dark" href="<?= site_url('pelanggan/booking/baru') ?>">Lihat jadwal &amp; booking</a>
        <?php endif; ?>
    </div>
    <?php if (! $studios): ?>
        <div class="empty-state">Belum ada studio yang tersedia. Admin dapat menambahkannya lewat panel admin.</div>
    <?php else: ?>
        <div class="row g-3 g-xl-4">
            <?php foreach ($studios as $s): ?>
                <div class="col-md-6 col-xl-4">
                    <article class="card studio-card">
                        <?php if ($s['photo']): ?>
                            <img src="<?= base_url('uploads/studios/' . esc($s['photo'], 'url')) ?>" class="studio-photo" alt="Foto <?= esc($s['name'], 'attr') ?>" loading="lazy">
                        <?php else: ?>
                            <div class="studio-photo studio-photo-empty">Foto studio belum tersedia</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3><?= esc($s['name']) ?></h3>
                            <p class="studio-description"><?= esc($s['description'] ?? '') ?></p>
                            <div class="studio-card-meta">
                                <div class="studio-price"><?= rupiah($s['hourly_rate']) ?> <small>/ jam</small></div>
                                <span class="capacity-pill"><?= (int) $s['capacity'] ?> orang</span>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section id="alat-sewa" class="home-section" aria-labelledby="equipment-heading">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Perlengkapan sesi</span>
            <h2 id="equipment-heading" class="section-title">Alat sewa</h2>
            <p>Lihat alat yang tersedia beserta stok dan tarif sewanya.</p>
        </div>
    </div>
    <?php if (! $equipments): ?>
        <div class="empty-state">Belum ada data alat sewa.</div>
    <?php else: ?>
        <div class="equipment-panel">
            <div class="equipment-panel-head">
                <div><strong>Daftar perlengkapan</strong><p>Tarif sewa ditampilkan per item.</p></div>
                <span class="equipment-count"><?= count($equipments) ?> item</span>
            </div>
            <div class="table-responsive">
                <table class="table studio-table align-middle" aria-labelledby="equipment-heading">
                    <thead><tr><th scope="col">Alat</th><th scope="col">Kategori</th><th scope="col" class="text-end">Stok</th><th scope="col" class="text-end">Harga sewa</th></tr></thead>
                    <tbody>
                    <?php foreach ($equipments as $e): ?>
                        <tr>
                            <td class="equipment-name"><?= esc($e['name']) ?></td>
                            <td><span class="equipment-category"><?= esc($e['category']) ?></span></td>
                            <td class="text-end equipment-stock"><?= (int) $e['stock'] ?></td>
                            <td class="text-end equipment-price"><?= rupiah($e['rental_price']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
