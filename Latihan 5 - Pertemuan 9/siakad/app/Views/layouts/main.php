<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SIAKAD') ?> | SIAKAD</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/siakad.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light siakad-nav sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url() ?>"><span class="brand-mark">S</span><span>SIAKAD</span></a>
        <?php if (session()->get('user')): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka menu"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav">
                <div class="navbar-nav me-auto ms-lg-4">
                    <?php if (session()->get('user')['role'] === 'admin'): ?>
                        <a class="nav-link" href="<?= site_url('admin') ?>">Dashboard</a>
                        <a class="nav-link" href="<?= site_url('admin/data/mahasiswa') ?>">Mahasiswa</a>
                        <a class="nav-link" href="<?= site_url('admin/data/dosen') ?>">Dosen</a>
                        <a class="nav-link" href="<?= site_url('admin/data/matakuliah') ?>">Mata Kuliah</a>
                        <a class="nav-link" href="<?= site_url('admin/data/prodi') ?>">Program Studi</a>
                        <a class="nav-link" href="<?= site_url('admin/data/kelas') ?>">Kelas</a>
                    <?php elseif (session()->get('user')['role'] === 'dosen'): ?>
                        <a class="nav-link" href="<?= site_url('dosen') ?>">Dashboard</a>
                        <a class="nav-link" href="<?= site_url('dosen/kelas') ?>">Kelas yang Diampu</a>
                        <a class="nav-link" href="<?= site_url('dosen/perwalian') ?>">Mahasiswa Perwalian</a>
                    <?php else: ?>
                        <a class="nav-link" href="<?= site_url('mahasiswa') ?>">Profil</a>
                        <a class="nav-link" href="<?= site_url('mahasiswa/frs') ?>">FRS</a>
                        <a class="nav-link" href="<?= site_url('mahasiswa/nilai') ?>">Nilai &amp; IPK</a>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-3 nav-account"><span class="small text-muted"><?= esc(session()->get('user')['name']) ?></span><form method="post" action="<?= site_url('logout') ?>" class="m-0"><?= csrf_field() ?><button class="btn btn-outline-secondary btn-sm" type="submit">Keluar</button></form></div>
            </div>
        <?php endif; ?>
    </div>
</nav>
<main class="container page-wrap">
    <?= view('partials/flash') ?>
    <?= $this->renderSection('content') ?>
</main>
<footer class="site-footer"><div class="container d-flex flex-wrap justify-content-between gap-2"><span>SIAKAD &middot; Sistem Informasi Akademik</span><span>&copy; <?= date('Y') ?></span></div></footer>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body></html>
