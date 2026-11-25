<?php

namespace App\Database\Seeds;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Teknologi', 'detail' => 'Teman produktif sehari-hari', 'mark' => '01'],
            ['name' => 'Gaya hidup', 'detail' => 'Pilihan nyaman untukmu', 'mark' => '02'],
            ['name' => 'Rumah', 'detail' => 'Buat ruang terasa lebih baik', 'mark' => '03'],
        ];

        $categoryModel = new CategoryModel();
        foreach ($categories as $category) {
            if ($categoryModel->where('name', $category['name'])->first() === null) {
                $categoryModel->insert($category);
            }
        }

        $products = [
            ['name' => 'Headphone Studio', 'category' => 'Teknologi', 'description' => 'Headphone nyaman untuk menemani fokus, bekerja, dan menikmati musik sepanjang hari.', 'price' => 489000, 'stock' => 18, 'image' => 'assets/img/products/headphone-studio.jpg'],
            ['name' => 'Tumbler Forma', 'category' => 'Gaya hidup', 'description' => 'Tumbler praktis dengan bentuk sederhana untuk membawa minuman favorit saat beraktivitas.', 'price' => 189000, 'stock' => 24, 'image' => 'assets/img/products/tumbler-forma.jpg'],
            ['name' => 'Lampu Meja Nusa', 'category' => 'Rumah', 'description' => 'Lampu meja dengan cahaya hangat untuk menemani waktu membaca atau bekerja di rumah.', 'price' => 325000, 'stock' => 12, 'image' => 'assets/img/products/lampu-meja-nusa.jpeg'],
            ['name' => 'Tas Belanja Kanvas', 'category' => 'Gaya hidup', 'description' => 'Tas kanvas ringan yang dapat digunakan kembali untuk membawa barang kebutuhan harian.', 'price' => 159000, 'stock' => 30, 'image' => 'assets/img/products/tas-belanja-kanvas.jpeg'],
            ['name' => 'Speaker Portabel', 'category' => 'Teknologi', 'description' => 'Speaker ringkas dengan konektivitas praktis untuk menghadirkan musik di berbagai suasana.', 'price' => 279000, 'stock' => 16, 'image' => 'assets/img/products/speaker-portabel.png'],
            ['name' => 'Diffuser Keramik', 'category' => 'Rumah', 'description' => 'Diffuser keramik dengan tampilan minimalis untuk membantu menciptakan suasana ruang yang tenang.', 'price' => 239000, 'stock' => 14, 'image' => 'assets/img/products/diffuser-keramik.jpg'],
        ];

        $productModel = new ProductModel();
        foreach ($products as $product) {
            if ($productModel->where('name', $product['name'])->first() !== null) {
                continue;
            }

            $category = $categoryModel->where('name', $product['category'])->first();
            if ($category === null) {
                continue;
            }

            $productModel->insert([
                'category_id' => (int) $category['id'],
                'name'        => $product['name'],
                'description' => $product['description'],
                'price'       => $product['price'],
                'stock'       => $product['stock'],
                'image'       => $product['image'],
                'status'      => 'active',
            ]);
        }
    }
}
