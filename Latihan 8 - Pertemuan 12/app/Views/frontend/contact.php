<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-intro page-width contact-intro">
    <div class="contact-intro-copy">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Kami senang mendengarmu</span>
        <h1>Yuk, <em>terhubung.</em></h1>
        <p>Ada yang ingin ditanyakan? Isi formulir di bawah ini, kami siap membantu.</p>
    </div>
    <aside class="contact-intro-card">
        <span class="contact-card-label">SAPAAN HANGAT DARI SHOPEASE</span>
        <strong>Selalu ada ruang untuk bertanya.</strong>
        <span class="contact-card-index">01 <i></i> 02</span>
    </aside>
</section>
<section class="contact-layout page-width">
    <aside class="contact-aside"><span class="eyebrow">Sapa ShopEase</span><h2>Setiap pesan berarti bagi kami.</h2><p>Kirim pertanyaan, saran, atau sekadar menyapa. Tim kami akan membalas secepat mungkin.</p><div class="contact-detail"><span class="detail-number">01</span><div><small>Email</small><a href="mailto:halo@shopease.id">halo@shopease.id</a></div></div><div class="contact-detail"><span class="detail-number">02</span><div><small>Jam layanan</small><span>Senin–Jumat, 09.00–17.00 WIB</span></div></div><span class="aside-accent" aria-hidden="true"></span></aside>
    <div class="contact-form-wrap">
        <div class="form-header"><div><span class="eyebrow">Formulir kontak</span><h2>Kami siap membantu.</h2></div><span class="form-step">01 — 03</span></div>
        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if ($errors): ?><div class="form-alert form-error" role="alert"><strong>Periksa kembali isianmu.</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" action="<?= url_to('contact.send') ?>" class="contact-form">
            <?= csrf_field() ?>
            <label for="name">Namamu <span>*</span><input id="name" name="name" type="text" value="<?= esc(old('name'), 'attr') ?>" placeholder="Contoh: Alya Putri" autocomplete="name" required></label>
            <label for="email">Alamat email <span>*</span><input id="email" name="email" type="email" value="<?= esc(old('email'), 'attr') ?>" placeholder="nama@email.com" autocomplete="email" required></label>
            <label for="message">Apa yang ingin kamu sampaikan? <span>*</span><textarea id="message" name="message" rows="5" placeholder="Tulis pesanmu di sini..." required><?= esc(old('message')) ?></textarea></label>
            <div class="form-bottom"><small>* Wajib diisi · Balasan biasanya dalam 1–2 hari kerja</small><button class="button button-accent" type="submit">Kirim pesan <span aria-hidden="true">↗</span></button></div>
        </form>
    </div>
</section>
<?= $this->endSection() ?>
