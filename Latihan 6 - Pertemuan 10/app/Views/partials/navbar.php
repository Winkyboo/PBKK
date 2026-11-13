<?php $active = $active ?? ''; ?>
<header class="site-header">
    <nav class="nav-shell" aria-label="Navigasi utama">
        <a class="brand" href="<?= url_to('home') ?>" aria-label="ShopEase, beranda">
            <span class="brand-mark" aria-hidden="true">s</span><span>shop<span class="brand-light">ease</span></span>
        </a>
        <input class="nav-toggle" type="checkbox" id="nav-toggle" aria-label="Buka menu">
        <label class="menu-button" for="nav-toggle" aria-hidden="true"><span></span><span></span></label>
        <div class="nav-links">
            <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="<?= url_to('home') ?>">Beranda</a>
            <a class="<?= $active === 'about' ? 'is-active' : '' ?>" href="<?= url_to('about') ?>">Tentang</a>
            <a class="<?= $active === 'contact' ? 'is-active' : '' ?>" href="<?= url_to('contact') ?>">Kontak</a>
            <a class="nav-cta" href="<?= url_to('home') ?>#koleksi">Jelajahi toko <span aria-hidden="true">↗</span></a>
        </div>
    </nav>
</header>
