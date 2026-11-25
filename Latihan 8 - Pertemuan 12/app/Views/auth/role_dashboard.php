<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="account-page page-width">
    <div class="account-heading"><span class="eyebrow"><span class="eyebrow-dot"></span> Area dengan akses terbatas</span><h1><?= esc($roleTitle) ?>.</h1><p><?= esc($description) ?></p></div>
    <div class="account-card role-dashboard-card">
        <span class="role-badge"><?= esc(session()->get('role')) ?></span>
        <h2>Otentikasi dan role berhasil diterapkan.</h2>
        <p>Route ini hanya dapat dibuka oleh akun yang memiliki role sesuai. Coba masuk menggunakan akun customer untuk melihat halaman 403.</p>
        <?php if (! empty($adminLinks)): ?>
            <div class="dashboard-actions">
                <a class="button button-accent" href="<?= url_to('admin.orders.index') ?>">Kelola pesanan <span aria-hidden="true">↗</span></a>
                <a class="button button-accent" href="<?= url_to('admin.products.index') ?>">Kelola produk <span aria-hidden="true">↗</span></a>
                <a class="button button-outline" href="<?= url_to('admin.categories.index') ?>">Kelola kategori <span aria-hidden="true">↗</span></a>
            </div>
        <?php endif; ?>
        <a class="button button-accent" href="<?= url_to('account') ?>">Lihat akun <span aria-hidden="true">↗</span></a>
    </div>
</section>
<?= $this->endSection() ?>
