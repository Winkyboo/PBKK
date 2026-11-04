<?php

namespace App\Controllers;

use App\Models\DosenModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Dosen extends BaseController
{
    public function dashboard()
    {
        $dosen = $this->dosen();
        $db = db_connect();
        return view('dosen/dashboard', [
            'title' => 'Dashboard Dosen',
            'jumlahKelas' => $db->table('kelas')->where('dosen_id', $dosen['id'])->countAllResults(),
            'jumlahPerwalian' => $db->table('mahasiswa')->where('dosen_wali_id', $dosen['id'])->countAllResults(),
            'kelas' => $this->classRows($dosen['id']),
        ]);
    }

    public function classes()
    {
        return view('dosen/classes', ['title' => 'Kelas yang Diampu', 'kelas' => $this->classRows($this->dosen()['id'])]);
    }

    public function classDetail(int $id)
    {
        $kelas = $this->findClass($id);
        $students = db_connect()->table('frs_detail f')->select('f.id AS frs_id,f.nilai_angka,f.nilai_huruf,m.nim,u.name AS mahasiswa_name')
            ->join('mahasiswa m', 'm.id=f.mahasiswa_id')->join('users u', 'u.id=m.user_id')
            ->where('f.kelas_id', $id)->orderBy('u.name')->get()->getResultArray();
        return view('dosen/class-detail', ['title' => 'Input Nilai', 'kelas' => $kelas, 'students' => $students]);
    }

    public function saveGrades(int $id)
    {
        $this->findClass($id);
        $scores = $this->request->getPost('nilai') ?? [];
        $db = db_connect();
        foreach ($scores as $frsId => $score) {
            if ($score === '') continue;
            if (! is_numeric($score) || (float) $score < 0 || (float) $score > 100) {
                return redirect()->back()->withInput()->with('error', 'Nilai harus berupa angka antara 0 sampai 100.');
            }
            $detail = $db->table('frs_detail')->where('id', (int) $frsId)->where('kelas_id', $id)->get()->getRowArray();
            if (! $detail) continue;
            $value = (float) $score;
            $letter = match (true) {
                $value >= 85 => 'A', $value >= 80 => 'A-', $value >= 75 => 'B+', $value >= 70 => 'B',
                $value >= 65 => 'B-', $value >= 60 => 'C+', $value >= 55 => 'C', $value >= 45 => 'D', default => 'E',
            };
            $db->table('frs_detail')->where('id', (int) $frsId)->update(['nilai_angka' => $value, 'nilai_huruf' => $letter]);
        }
        return redirect()->to(site_url('dosen/kelas/' . $id))->with('success', 'Nilai berhasil disimpan.');
    }

    public function advisees()
    {
        $dosen = $this->dosen();
        $rows = db_connect()->table('mahasiswa m')->select('m.*,u.name,u.email,p.nama AS prodi_name')
            ->join('users u', 'u.id=m.user_id')->join('program_studi p', 'p.id=m.prodi_id')
            ->where('m.dosen_wali_id', $dosen['id'])->orderBy('u.name')->get()->getResultArray();
        return view('dosen/advisees', ['title' => 'Mahasiswa Perwalian', 'mahasiswa' => $rows]);
    }

    private function dosen(): array
    {
        return model(DosenModel::class)->where('user_id', session()->get('user')['id'])->first()
            ?? throw PageNotFoundException::forPageNotFound('Profil dosen belum tersedia.');
    }

    private function classRows(int $dosenId): array
    {
        return db_connect()->table('kelas k')->select('k.*,mk.kode,mk.nama AS mata_kuliah,mk.sks,COUNT(f.id) AS jumlah_mahasiswa')
            ->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')->join('dosen d', 'd.id=k.dosen_id')
            ->join('frs_detail f', 'f.kelas_id=k.id', 'left')->where('k.dosen_id', $dosenId)
            ->groupBy('k.id,k.mata_kuliah_id,k.dosen_id,k.semester,k.tahun_ajaran,k.nama_kelas,k.kuota,mk.kode,mk.nama,mk.sks')
            ->orderBy('k.tahun_ajaran', 'DESC')->orderBy('k.semester')->get()->getResultArray();
    }

    private function findClass(int $id): array
    {
        $row = db_connect()->table('kelas k')->select('k.*,mk.kode,mk.nama AS mata_kuliah,mk.sks,u.name AS dosen_name')
            ->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')->join('dosen d', 'd.id=k.dosen_id')
            ->join('users u', 'u.id=d.user_id')->where('k.id', $id)->where('d.user_id', session()->get('user')['id'])->get()->getRowArray();
        return $row ?? throw PageNotFoundException::forPageNotFound('Kelas tidak ditemukan atau bukan kelas yang Anda ampu.');
    }
}
