<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Belanja mudah, hidup lebih ringan',
            'active' => 'home',
            'categories' => [
                ['name' => 'Teknologi', 'detail' => 'Teman produktif sehari-hari', 'mark' => '01'],
                ['name' => 'Gaya hidup', 'detail' => 'Pilihan nyaman untukmu', 'mark' => '02'],
                ['name' => 'Rumah', 'detail' => 'Buat ruang terasa lebih baik', 'mark' => '03'],
            ],
            'products' => [
                ['name' => 'Headphone Studio', 'category' => 'Teknologi', 'price' => 489000, 'tone' => 'product-sand', 'image' => 'assets/img/products/headphone-studio.jpg', 'fallback' => 'assets/img/product-headphones.svg'],
                ['name' => 'Tumbler Forma', 'category' => 'Gaya hidup', 'price' => 189000, 'tone' => 'product-peach', 'image' => 'assets/img/products/tumbler-forma.jpg', 'fallback' => 'assets/img/product-tumbler.svg'],
                ['name' => 'Lampu Meja Nusa', 'category' => 'Rumah', 'price' => 325000, 'tone' => 'product-olive', 'image' => 'assets/img/products/lampu-meja-nusa.jpeg', 'fallback' => 'assets/img/product-lamp.svg'],
                ['name' => 'Tas Belanja Kanvas', 'category' => 'Gaya hidup', 'price' => 159000, 'tone' => 'product-peach', 'image' => 'assets/img/products/tas-belanja-kanvas.jpeg', 'fallback' => 'assets/img/shop-hero.svg'],
                ['name' => 'Speaker Portabel', 'category' => 'Teknologi', 'price' => 279000, 'tone' => 'product-sand', 'image' => 'assets/img/products/speaker-portabel.png', 'fallback' => 'assets/img/product-headphones.svg'],
                ['name' => 'Diffuser Keramik', 'category' => 'Rumah', 'price' => 239000, 'tone' => 'product-olive', 'image' => 'assets/img/products/diffuser-keramik.jpg', 'fallback' => 'assets/img/product-lamp.svg'],
            ],
        ];

        return view('frontend/home', $data);
    }
}
