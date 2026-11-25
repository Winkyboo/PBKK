<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$roleLabels = [
    'admin' => 'Administrator',
    'operator' => 'Operator',
    'customer' => 'Customer',
];
?>
<section class="account-page page-width">
    <div class="account-heading"><span class="eyebrow"><span class="eyebrow-dot"></span> Area akun</span><h1>Halo, <em><?= esc($user['name']) ?>.</em></h1><p>Informasi akun ini hanya tersedia setelah kamu masuk.</p></div>
    <div class="account-card">
        <div class="account-card-heading"><span class="account-avatar" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span><div><span class="eyebrow">Profil ShopEase</span><h2><?= esc($user['name']) ?></h2></div></div>
        <dl class="account-details">
            <div><dt>Email</dt><dd><?= esc($user['email']) ?></dd></div>
            <div><dt>Peran</dt><dd><span class="role-badge"><?= esc($roleLabels[$user['role']] ?? 'Pengguna') ?></span></dd></div>
        </dl>
        <a class="button button-accent" href="<?= url_to('home') ?>">Kembali ke beranda <span aria-hidden="true">↗</span></a>
        <?php if ($user['role'] === 'customer'): ?><a class="button button-outline account-orders-link" href="<?= url_to('orders.index') ?>">Lihat pesanan saya <span aria-hidden="true">↗</span></a><?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
