<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="about-hero page-width">
    <div class="about-hero-copy">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Sedikit tentang kami</span>
        <h1>Belanja yang terasa<br><em>lebih manusiawi.</em></h1>
        <p>ShopEase hadir untuk membuat menemukan kebutuhan sehari-hari terasa lebih mudah, tenang, dan menyenangkan.</p>
        <div class="about-hero-note"><span>01</span><span>Kurasi yang membuat hari terasa lebih ringan</span></div>
    </div>
    <div class="about-hero-art">
        <div class="about-art-top"><span>PILIH DENGAN PENUH ARTI</span><span>SE. / 01</span></div>
        <img src="<?= base_url('assets/img/shop-hero.svg') ?>" alt="Ilustrasi tas belanja ShopEase dengan pilihan barang sehari-hari">
        <div class="about-art-bottom"><span class="art-pill">PILIHAN SEHARI-HARI</span><strong>Ruang untuk<br>hal-hal baik.</strong></div>
    </div>
</section>
<section class="about-story page-width">
    <div class="story-heading"><span class="eyebrow">Cerita ShopEase</span><span class="story-number">01 <i></i> 03</span></div>
    <div class="story-copy">
        <h2>Barang yang baik memberi ruang untuk hidup yang lebih baik.</h2>
        <div class="story-paragraphs">
            <p>Kami percaya belanja tidak harus terasa ramai. ShopEase mengumpulkan pilihan yang berguna, nyaman, dan dibuat dengan perhatian agar kamu bisa menemukan yang dibutuhkan tanpa kehilangan waktu.</p>
            <p>Mulai dari teknologi, gaya hidup, sampai barang untuk rumah, setiap pilihan kami hadirkan dengan informasi yang jelas dan pengalaman yang sederhana.</p>
        </div>
    </div>
</section>
<section class="values-section page-width"><div class="section-heading"><div><span class="eyebrow">Yang kami pegang</span><h2>Sederhana, sejak awal.</h2></div></div><div class="values-grid">
    <article><span>01</span><h3>Pilihan yang berarti</h3><p>Kami mengutamakan produk yang bermanfaat dan cocok menemani keseharian.</p></article>
    <article><span>02</span><h3>Jelas dan jujur</h3><p>Informasi produk disampaikan dengan ringkas supaya kamu tahu apa yang dipilih.</p></article>
    <article><span>03</span><h3>Mudah untuk semua</h3><p>Setiap bagian dirancang agar belanja terasa ringan di berbagai perangkat.</p></article>
</div></section>
<section class="about-cta page-width"><p>Punya pertanyaan atau ingin menyapa?</p><a class="button button-accent" href="<?= url_to('contact') ?>">Hubungi kami <span aria-hidden="true">↗</span></a></section>
<?= $this->endSection() ?>
