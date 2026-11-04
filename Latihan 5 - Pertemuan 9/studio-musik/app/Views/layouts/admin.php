<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | Admin Studio Musik</title>
    <link href="<?= base_url('assets/css/sb-admin.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/studio.css') ?>" rel="stylesheet">
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark">
    <a class="navbar-brand ps-3 hand" href="<?= site_url('admin') ?>">Studio Musik</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button" aria-label="Buka atau tutup menu samping">Menu</button>
    <div class="ms-auto me-3 d-flex align-items-center gap-2">
        <span class="nav-user"><?= esc(current_user()['name']) ?></span>
        <form action="<?= site_url('keluar') ?>" method="post" class="m-0">
            <?= csrf_field() ?>
            <button class="btn btn-outline-dark btn-sm" type="submit">Keluar</button>
        </form>
    </div>
</nav>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <?php $seg = service('uri')->getSegment(2); ?>
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Ringkasan</div>
                    <a class="nav-link <?= $seg === '' ? 'active' : '' ?>" href="<?= site_url('admin') ?>">Dashboard</a>
                    <div class="sb-sidenav-menu-heading">Data</div>
                    <a class="nav-link <?= $seg === 'studio' ? 'active' : '' ?>" href="<?= site_url('admin/studio') ?>">Studio</a>
                    <a class="nav-link <?= $seg === 'alat' ? 'active' : '' ?>" href="<?= site_url('admin/alat') ?>">Alat sewa</a>
                    <div class="sb-sidenav-menu-heading">Transaksi</div>
                    <a class="nav-link <?= $seg === 'booking' ? 'active' : '' ?>" href="<?= site_url('admin/booking') ?>">Booking</a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Masuk sebagai</div>
                Administrator
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 pb-4">
                <h1 class="page-title hand"><?= $this->renderSection('heading') ?></h1>
                <?= view('partials/flash') ?>
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>
</div>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/sb-admin.js') ?>"></script>
</body>
</html>
