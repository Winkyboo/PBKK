<?php

namespace App\Models;

use CodeIgniter\Model;

class FrsDetailModel extends Model
{
    protected $table = 'frs_detail';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['mahasiswa_id', 'kelas_id', 'nilai_angka', 'nilai_huruf'];
    protected $useTimestamps = false;
}
