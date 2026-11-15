<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="account-page page-width">
    <div class="error-card">
        <span class="error-code">403</span>
        <span class="eyebrow">Halaman terbatas</span>
        <h1>Akses ini belum tersedia untuk peranmu.</h1>
        <p>Akun dengan role <strong><?= esc($role ?? 'pengguna') ?></strong> tidak diizinkan membuka halaman tersebut.</p>
        <a class="button button-accent" href="<?= url_to('account') ?>">Kembali ke akun <span aria-hidden="true">↗</span></a>
    </div>
</section>
<?= $this->endSection() ?>
