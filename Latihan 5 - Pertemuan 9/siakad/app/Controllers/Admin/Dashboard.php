<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $currentYear = date('Y') . '/' . (date('Y') + 1);
        $classes = $db->table('kelas k')->select('k.*, mk.kode, mk.nama AS mata_kuliah, mk.sks, u.name AS dosen_name')
            ->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')->join('dosen d', 'd.id=k.dosen_id')
            ->join('users u', 'u.id=d.user_id')->where('k.tahun_ajaran', $currentYear)->orderBy('mk.nama')->get()->getResultArray();
        return view('admin/dashboard', [
            'title' => 'Dashboard Admin',
            'jumlahMahasiswa' => $db->table('mahasiswa')->countAllResults(),
            'jumlahDosen' => $db->table('dosen')->countAllResults(),
            'jumlahMK' => $db->table('mata_kuliah')->countAllResults(),
            'jumlahKelas' => $db->table('kelas')->countAllResults(),
            'kelas' => $classes,
        ]);
    }
}
