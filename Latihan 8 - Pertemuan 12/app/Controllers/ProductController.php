<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProductController extends BaseController
{
    public function index(): string
    {
        $productModel = new ProductModel();
        $filters       = [
            'q'        => trim((string) $this->request->getGet('q')),
            'category' => (int) ($this->request->getGet('category') ?? 0),
            'sort'     => (string) ($this->request->getGet('sort') ?? ''),
        ];

        return view('frontend/catalog', [
            'title'      => 'Katalog produk',
            'active'     => 'catalog',
            'products'   => $productModel->catalog($filters),
            'pager'      => $productModel->pager,
            'categories' => (new CategoryModel())->orderBy('name', 'ASC')->findAll(),
            'filters'    => $filters,
        ]);
    }

    public function show(int $id): string
    {
        $product = (new ProductModel())->findActiveWithCategory($id);
        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan atau sedang tidak tersedia.');
        }

        return view('frontend/product_detail', [
            'title'   => $product['name'],
            'active'  => 'catalog',
            'product' => $product,
        ]);
    }
}
