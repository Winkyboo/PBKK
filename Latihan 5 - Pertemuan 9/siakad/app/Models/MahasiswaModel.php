<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'nim', 'prodi_id', 'dosen_wali_id', 'angkatan'];
    protected $useTimestamps = false;
}
