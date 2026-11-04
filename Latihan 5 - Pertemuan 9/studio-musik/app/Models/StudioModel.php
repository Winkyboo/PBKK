<?php

namespace App\Models;

use CodeIgniter\Model;

class StudioModel extends Model
{
    protected $table            = 'studios';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'description', 'hourly_rate', 'capacity', 'photo', 'is_active'];
    protected $useTimestamps    = true;
}
