<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?>Masuk<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="auth-box">
    <h1 class="hand page-title">Masuk</h1>
    <form action="<?= site_url('login') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email'), 'attr') ?>" autocomplete="email" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Kata sandi</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Masuk</button>
    </form>
    <p class="mt-3 mb-0 small">Belum punya akun? <a href="<?= site_url('daftar') ?>">Daftar sebagai pelanggan</a></p>
</div>
<?= $this->endSection() ?>
