<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'detail', 'mark'];

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]',
        'detail' => 'required|min_length[5]|max_length[180]',
        'mark'   => 'required|max_length[5]',
    ];
}
