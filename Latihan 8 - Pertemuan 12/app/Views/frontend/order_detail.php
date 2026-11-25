<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$statusLabels = ['pending' => 'Menunggu diproses', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Diterima'];
?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact"><div><span class="eyebrow"><span class="eyebrow-dot"></span> Detail pesanan</span><h1><?= esc($order['order_number']) ?><em>.</em></h1><p>Dibuat pada <?= esc(date('d M Y, H:i', strtotime($order['created_at']))) ?>.</p></div><a class="text-link" href="<?= url_to('orders.index') ?>">Semua pesanan <span aria-hidden="true">↗</span></a></div>
    <div class="order-status-banner"><span class="order-status order-status-<?= esc($order['status'], 'attr') ?>"><?= esc($statusLabels[$order['status']] ?? $order['status']) ?></span><p><?= $order['status'] === 'shipped' ? 'Pesanan sedang dalam perjalanan.' : ($order['status'] === 'delivered' ? 'Pesanan telah diterima.' : 'Admin sedang menyiapkan pesananmu.') ?></p></div>
    <div class="order-detail-layout">
        <div class="order-detail-main">
            <section class="order-panel"><span class="eyebrow">Produk pesanan</span><?php foreach ($items as $item): ?><article class="order-line"><img src="<?= esc(shop_product_image_url($item['product_image']), 'attr') ?>" alt=""><div><strong><?= esc($item['product_name']) ?></strong><small><?= (int) $item['quantity'] ?> × Rp <?= number_format((float) $item['unit_price'], 0, ',', '.') ?></small></div><b>Rp <?= number_format((float) $item['line_total'], 0, ',', '.') ?></b></article><?php endforeach; ?></section>
            <section class="order-panel"><span class="eyebrow">Alamat pengiriman</span><strong><?= esc($order['customer_name']) ?></strong><p><?= esc($order['phone']) ?><br><?= nl2br(esc($order['shipping_address'])) ?><br><?= esc($order['city']) ?>, <?= esc($order['postal_code']) ?></p><div class="order-shipping-info"><span><?= esc(ucfirst($order['shipping_method'])) ?> · <?= $order['shipping_method'] === 'express' ? '1–2' : '3–5' ?> hari kerja</span><?php if ($order['tracking_number']): ?><strong>Resi: <?= esc($order['tracking_number']) ?></strong><?php else: ?><small>Nomor resi muncul setelah pesanan dikirim.</small><?php endif; ?></div></section>
        </div>
        <aside class="cart-summary"><span class="eyebrow">Ringkasan pembayaran</span><div><span>Subtotal produk</span><strong>Rp <?= number_format((float) $order['subtotal'], 0, ',', '.') ?></strong></div><div><span>Pengiriman</span><strong>Rp <?= number_format((float) $order['shipping_cost'], 0, ',', '.') ?></strong></div><div class="order-total"><span>Total</span><strong>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></strong></div><div><span>Pembayaran</span><strong>COD · <?= $order['payment_status'] === 'paid' ? 'Lunas' : 'Belum dibayar' ?></strong></div></aside>
    </div>
</section>
<?= $this->endSection() ?>
