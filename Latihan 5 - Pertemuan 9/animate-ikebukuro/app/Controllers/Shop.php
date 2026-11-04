<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Shop extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $builder = $db->table('products p')->select('p.*,c.name AS category_name')->join('categories c', 'c.id=p.category_id')->where('p.stock >', 0);
        $search = trim((string) $this->request->getGet('q'));
        $category = (int) $this->request->getGet('category');
        if ($search !== '') $builder->groupStart()->like('p.name', $search)->orLike('p.description', $search)->groupEnd();
        if ($category > 0) $builder->where('p.category_id', $category);
        $products = $builder->orderBy('p.is_featured', 'DESC')->orderBy('p.id', 'DESC')->get()->getResultArray();
        $featured = model(ProductModel::class)->where('is_featured', 1)->where('stock >', 0)->orderBy('id', 'DESC')->first();
        return view('shop/index', ['title' => 'Anime goods & collectibles', 'products' => $products, 'featured' => $featured, 'categories' => model(CategoryModel::class)->orderBy('name')->findAll(), 'search' => $search, 'selectedCategory' => $category]);
    }

    public function show(int $id)
    {
        $product = db_connect()->table('products p')->select('p.*,c.name AS category_name')->join('categories c', 'c.id=p.category_id')->where('p.id', $id)->get()->getRowArray();
        if (! $product) throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        return view('shop/show', ['title' => $product['name'], 'product' => $product]);
    }
}
