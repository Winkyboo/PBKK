<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | Studio Musik</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/studio.css') ?>" rel="stylesheet">
</head>
<body>
<a class="visually-hidden-focusable skip-link" href="#main">Lewati ke konten utama</a>
<nav class="navbar navbar-expand-md studio-nav">
    <div class="container">
        <a class="navbar-brand hand" href="<?= site_url('/') ?>">Studio Musik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Beranda</a></li>
                <?php if (has_role('pelanggan')): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('pelanggan/booking') ?>">Booking saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('pelanggan/booking/baru') ?>">Ajukan booking</a></li>
                <?php elseif (has_role('admin')): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('admin') ?>">Panel admin</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if (current_user()): ?>
                    <span class="nav-user"><?= esc(current_user()['name']) ?></span>
                    <form action="<?= site_url('keluar') ?>" method="post" class="m-0">
                        <?= csrf_field() ?>
                        <button class="btn btn-outline-dark btn-sm" type="submit">Keluar</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-outline-dark btn-sm" href="<?= site_url('login') ?>">Masuk</a>
                    <a class="btn btn-dark btn-sm" href="<?= site_url('daftar') ?>">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main id="main" class="container py-4">
    <?= view('partials/flash') ?>
    <?= $this->renderSection('content') ?>
</main>

<footer class="site-footer">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><strong>Studio Musik</strong> <span class="text-secondary">&middot; Ruang untuk setiap sesi.</span></span>
        <span>&copy; <?= date('Y') ?> Studio Musik</span>
    </div>
</footer>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
