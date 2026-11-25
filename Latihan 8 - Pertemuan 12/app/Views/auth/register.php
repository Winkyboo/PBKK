<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $old = session()->getFlashdata('old_values') ?? []; ?>
<section class="auth-layout auth-layout-register page-width">
    <aside class="auth-intro">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Mulai perjalananmu</span>
        <h1>Hal baik dimulai dari <em>akun baru.</em></h1>
        <p>Buat akun ShopEase untuk menyimpan pengalaman belanjamu dalam satu tempat.</p>
        <div class="auth-intro-note"><span class="auth-note-mark">02</span><span>Registrasi publik membuat akun customer.</span></div>
    </aside>
    <div class="auth-panel">
        <div class="auth-panel-heading">
            <div><span class="eyebrow">Registrasi customer</span><h2>Buat akunmu.</h2></div>
            <span class="auth-step">02 / 02</span>
        </div>
        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if ($errors): ?>
            <div class="form-alert form-error" role="alert">
                <strong>Periksa kembali isianmu.</strong>
                <ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form action="<?= url_to('register.attempt') ?>" method="post" class="auth-form auth-form-register">
            <?= csrf_field() ?>
            <label for="name">Nama lengkap
                <input id="name" name="name" type="text" value="<?= esc($old['name'] ?? '', 'attr') ?>" placeholder="Contoh: Alya Putri" autocomplete="name" required>
            </label>
            <label for="email">Email
                <input id="email" name="email" type="email" value="<?= esc($old['email'] ?? '', 'attr') ?>" placeholder="nama@email.com" autocomplete="email" required>
            </label>
            <label for="phone">Nomor telepon <small>Opsional</small>
                <input id="phone" name="phone" type="tel" value="<?= esc($old['phone'] ?? '', 'attr') ?>" placeholder="08xxxxxxxxxx" autocomplete="tel">
            </label>
            <label for="password">Kata sandi
                <input id="password" name="password" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
            </label>
            <label for="password_confirm" class="register-full">Konfirmasi kata sandi
                <input id="password_confirm" name="password_confirm" type="password" placeholder="Ketik ulang kata sandi" autocomplete="new-password" required>
            </label>
            <button class="button button-accent auth-submit register-full" type="submit">Buat akun <span aria-hidden="true">↗</span></button>
        </form>
        <p class="auth-switch">Sudah punya akun? <a href="<?= url_to('login') ?>">Masuk</a></p>
    </div>
</section>
<?= $this->endSection() ?>
