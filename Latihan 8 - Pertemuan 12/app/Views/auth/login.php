<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="auth-layout page-width">
    <aside class="auth-intro">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Senang melihatmu kembali</span>
        <h1>Masuk dan lanjutkan <em>ceritamu.</em></h1>
        <p>Temukan lagi pilihan yang membuat keseharian terasa lebih mudah bersama ShopEase.</p>
        <div class="auth-intro-note"><span class="auth-note-mark">01</span><span>Belanja nyaman dimulai dari sini.</span></div>
    </aside>
    <div class="auth-panel">
        <div class="auth-panel-heading">
            <div><span class="eyebrow">Masuk ke akun</span><h2>Selamat datang.</h2></div>
            <span class="auth-step">01 / 02</span>
        </div>
        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if ($errors): ?>
            <div class="form-alert form-error" role="alert">
                <strong>Periksa kembali isianmu.</strong>
                <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form action="<?= url_to('login.attempt') ?>" method="post" class="auth-form">
            <?= csrf_field() ?>
            <label for="email">Email
                <input id="email" name="email" type="email" value="<?= esc(session()->getFlashdata('old_email') ?? '', 'attr') ?>" placeholder="nama@email.com" autocomplete="email" required>
            </label>
            <label for="password">Kata sandi
                <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
            </label>
            <button class="button button-accent auth-submit" type="submit">Masuk <span aria-hidden="true">↗</span></button>
        </form>
        <p class="auth-switch">Belum punya akun? <a href="<?= url_to('register') ?>">Daftar di sini</a></p>
    </div>
</section>
<?= $this->endSection() ?>
