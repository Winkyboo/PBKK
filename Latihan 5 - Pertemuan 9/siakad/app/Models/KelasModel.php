<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['mata_kuliah_id', 'dosen_id', 'semester', 'tahun_ajaran', 'nama_kelas', 'kuota'];
    protected $useTimestamps = false;
}
