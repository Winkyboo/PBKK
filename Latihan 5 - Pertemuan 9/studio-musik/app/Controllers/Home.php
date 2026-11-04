<?php

namespace App\Controllers;

use App\Models\EquipmentModel;
use App\Models\StudioModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index', [
            'studios'    => model(StudioModel::class)->where('is_active', 1)->orderBy('name', 'ASC')->findAll(),
            'equipments' => model(EquipmentModel::class)->orderBy('name', 'ASC')->findAll(),
        ]);
    }
}
