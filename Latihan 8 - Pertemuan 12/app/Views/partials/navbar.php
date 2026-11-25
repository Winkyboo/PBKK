<?php
$active = $active ?? '';
$loggedIn = (bool) session()->get('logged_in');
$role = session()->get('role');
$cart = session()->get('cart');
$cartCount = is_array($cart) ? array_sum(array_map('intval', $cart)) : 0;
?>
<header class="site-header">
    <nav class="nav-shell" aria-label="Navigasi utama">
        <a class="brand" href="<?= url_to('home') ?>" aria-label="ShopEase, beranda">
            <span class="brand-mark" aria-hidden="true">s</span><span>shop<span class="brand-light">ease</span></span>
        </a>
        <input class="nav-toggle" type="checkbox" id="nav-toggle" aria-label="Buka menu">
        <label class="menu-button" for="nav-toggle" aria-hidden="true"><span></span><span></span></label>
        <div class="nav-links">
            <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="<?= url_to('home') ?>">Beranda</a>
            <a class="<?= $active === 'catalog' ? 'is-active' : '' ?>" href="<?= url_to('catalog') ?>">Produk</a>
            <a class="<?= $active === 'about' ? 'is-active' : '' ?>" href="<?= url_to('about') ?>">Tentang</a>
            <a class="<?= $active === 'contact' ? 'is-active' : '' ?>" href="<?= url_to('contact') ?>">Kontak</a>
            <a class="nav-cart <?= $active === 'cart' ? 'is-active' : '' ?>" href="<?= url_to('cart') ?>">Keranjang <span class="cart-count"><?= (int) $cartCount ?></span></a>

            <div class="nav-auth-actions">
                <?php if ($loggedIn): ?>
                    <?php if ($role === 'admin'): ?>
                        <a href="<?= url_to('admin.dashboard') ?>">Dashboard</a>
                        <a href="<?= url_to('admin.orders.index') ?>">Pesanan</a>
                        <a href="<?= url_to('admin.products.index') ?>">Produk</a>
                        <a href="<?= url_to('admin.categories.index') ?>">Kategori</a>
                    <?php elseif ($role === 'operator'): ?>
                        <a href="<?= url_to('operator.dashboard') ?>">Dashboard</a>
                    <?php elseif ($role === 'customer'): ?>
                        <a href="<?= url_to('orders.index') ?>">Pesanan saya</a>
                    <?php endif; ?>
                    <a class="nav-user" href="<?= url_to('account') ?>"><?= esc(session()->get('user_name') ?? 'Akun') ?></a>
                    <form action="<?= url_to('logout') ?>" method="post" class="nav-logout-form">
                        <?= csrf_field() ?>
                        <button class="nav-logout" type="submit">Keluar</button>
                    </form>
                <?php else: ?>
                    <a href="<?= url_to('login') ?>" class="nav-login">Masuk</a>
                    <a href="<?= url_to('register') ?>" class="nav-cta">Buat akun <span aria-hidden="true">↗</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
