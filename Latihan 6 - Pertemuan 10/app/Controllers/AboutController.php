<?php

namespace App\Controllers;

class AboutController extends BaseController
{
    public function index(): string
    {
        return view('frontend/about', [
            'title' => 'Tentang ShopEase',
            'active' => 'about',
        ]);
    }
}
