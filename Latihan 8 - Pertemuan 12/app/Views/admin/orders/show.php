<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $statusLabels = ['pending' => 'Menunggu diproses', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Diterima']; ?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact"><div><span class="eyebrow"><span class="eyebrow-dot"></span> Panel admin / Pesanan</span><h1><?= esc($order['order_number']) ?><em>.</em></h1><p>Atur status proses, pembayaran, dan resi pengiriman.</p></div><a class="text-link" href="<?= url_to('admin.orders.index') ?>">Kembali ke pesanan <span aria-hidden="true">↗</span></a></div>
    <div class="order-detail-layout">
        <div class="order-detail-main">
            <section class="order-panel"><span class="eyebrow">Penerima</span><strong><?= esc($order['customer_name']) ?></strong><p><?= esc($order['customer_email']) ?><br><?= esc($order['phone']) ?><br><?= nl2br(esc($order['shipping_address'])) ?><br><?= esc($order['city']) ?>, <?= esc($order['postal_code']) ?></p><div class="order-shipping-info"><span><?= esc(ucfirst($order['shipping_method'])) ?> · ongkir Rp <?= number_format((float) $order['shipping_cost'], 0, ',', '.') ?></span><?php if ($order['tracking_number']): ?><strong>Resi: <?= esc($order['tracking_number']) ?></strong><?php endif; ?></div></section>
            <section class="order-panel"><span class="eyebrow">Rincian produk</span><?php foreach ($items as $item): ?><article class="order-line"><img src="<?= esc(shop_product_image_url($item['product_image']), 'attr') ?>" alt=""><div><strong><?= esc($item['product_name']) ?></strong><small><?= (int) $item['quantity'] ?> × Rp <?= number_format((float) $item['unit_price'], 0, ',', '.') ?></small></div><b>Rp <?= number_format((float) $item['line_total'], 0, ',', '.') ?></b></article><?php endforeach; ?><div class="admin-order-total"><span>Total pesanan</span><strong>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></strong></div></section>
        </div>
        <form class="management-form order-update-form" action="<?= url_to('admin.orders.update', $order['id']) ?>" method="post">
            <?= csrf_field() ?><input type="hidden" name="_method" value="PUT">
            <span class="eyebrow">Perbarui status</span>
            <label>Status pesanan<select name="status" required><?php foreach ($statuses as $status): ?><option value="<?= esc($status, 'attr') ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= esc($statusLabels[$status]) ?></option><?php endforeach; ?></select></label>
            <label>Nomor resi<input type="text" name="tracking_number" maxlength="60" value="<?= esc($order['tracking_number'] ?? '', 'attr') ?>" placeholder="Diisi sebelum status dikirim"></label>
            <label>Status pembayaran<select name="payment_status" required><option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Belum dibayar</option><option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Lunas</option></select></label>
            <p class="order-payment-note">Metode pembayaran customer: COD (bayar di tempat).</p>
            <div class="management-form-actions"><button class="button button-accent" type="submit">Simpan status <span aria-hidden="true">↗</span></button></div>
        </form>
    </div>
</section>
<?= $this->endSection() ?>
