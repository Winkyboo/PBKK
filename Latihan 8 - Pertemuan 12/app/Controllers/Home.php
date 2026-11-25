<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('frontend/home', [
            'title' => 'Belanja mudah, hidup lebih ringan',
            'active' => 'home',
            'categories' => (new CategoryModel())->orderBy('id', 'ASC')->findAll(),
            'products'   => (new ProductModel())->activeForHome(),
        ]);
    }
}
