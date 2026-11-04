<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading">
    <div><span class="eyebrow">Your little haul</span><h1>Keranjang</h1><p>Pastikan koleksimu sudah sesuai sebelum checkout.</p></div>
    <a class="btn btn-outline-secondary" href="<?= site_url('#shop') ?>">Lanjut belanja</a>
</div>
<?php if (!$items): ?>
    <div class="empty-cart"><div class="empty-cart-mark">✳</div><h2>Keranjangmu masih kosong.</h2><p>Temukan artbook atau collectible baru untuk dibawa pulang.</p><a class="btn btn-dark" href="<?= site_url('#shop') ?>">Lihat koleksi</a></div>
<?php else: ?>
    <?php $total = 0; foreach ($items as $item) $total += (float) $item['subtotal']; ?>
    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="cart-panel">
                <form method="post" action="<?= site_url('keranjang/ubah') ?>">
                    <?= csrf_field() ?>
                    <div class="table-responsive"><table class="table cart-table align-middle mb-0">
                        <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr></thead>
                        <tbody><?php foreach ($items as $item): ?><tr>
                            <td><div class="cart-product"><img src="<?= base_url($item['image'] ?: 'assets/img/product-placeholder.svg') ?>" alt=""><div><a href="<?= site_url('produk/' . (int) $item['id']) ?>"><strong><?= esc($item['name']) ?></strong></a><small><?= (int) $item['stock'] ?> stok tersedia</small></div></div></td>
                            <td><?= rupiah($item['price']) ?></td>
                            <td><input class="form-control quantity-input" type="number" min="0" max="<?= (int) $item['stock'] ?>" name="quantity[<?= (int) $item['id'] ?>]" value="<?= (int) $item['quantity'] ?>" aria-label="Jumlah <?= esc($item['name'], 'attr') ?>"></td>
                            <td class="fw-bold"><?= rupiah($item['subtotal']) ?></td>
                            <td><button class="remove-link" type="submit" formaction="<?= site_url('keranjang/' . (int) $item['id'] . '/hapus') ?>">Hapus</button></td>
                        </tr><?php endforeach ?></tbody>
                    </table></div>
                    <div class="cart-table-foot"><span class="text-muted small">Set jumlah menjadi 0 untuk menghapus produk.</span><button class="btn btn-outline-secondary btn-sm" type="submit">Perbarui keranjang</button></div>
                </form>
            </section>
        </div>
        <div class="col-lg-4"><aside class="order-summary"><span class="eyebrow">Order summary</span><h2>Ringkasan</h2><div class="summary-line"><span><?= array_sum(array_column($items, 'quantity')) ?> item</span><strong><?= rupiah($total) ?></strong></div><div class="summary-line summary-total"><span>Total sementara</span><strong><?= rupiah($total) ?></strong></div><p>Ongkir dan detail pengiriman dikonfirmasi saat checkout.</p><?php if ((session()->get('user')['role'] ?? null) === 'customer'): ?><a class="btn btn-dark w-100" href="<?= site_url('pelanggan/checkout') ?>">Lanjut checkout <span>→</span></a><?php else: ?><a class="btn btn-dark w-100" href="<?= site_url('login') ?>">Masuk untuk checkout</a><?php endif ?></aside></div>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
