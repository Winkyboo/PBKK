<?php

namespace App\Controllers;

use App\Models\MahasiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Mahasiswa extends BaseController
{
    public function profile()
    {
        return view('mahasiswa/profile', ['title' => 'Profil Mahasiswa', 'profile' => $this->profileData()]);
    }

    public function frs()
    {
        $profile = $this->profileData();
        [$year, $semester] = $this->currentTerm();
        return view('mahasiswa/frs', [
            'title' => 'Formulir Rencana Studi', 'profile' => $profile, 'year' => $year, 'semester' => $semester,
            'enrolled' => $this->frsRows($profile['id'], $year, $semester),
            'available' => $this->availableClasses($profile, $year, $semester),
        ]);
    }

    public function addClass()
    {
        $profile = $this->profileData();
        $classId = (int) $this->request->getPost('kelas_id');
        [$year, $semester] = $this->currentTerm();
        $class = db_connect()->table('kelas k')->select('k.*,mk.prodi_id,mk.sks')
            ->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')->where('k.id', $classId)
            ->where('k.tahun_ajaran', $year)->where('k.semester', $semester)->get()->getRowArray();
        if (! $class || (int) $class['prodi_id'] !== (int) $profile['prodi_id']) {
            return redirect()->to(site_url('mahasiswa/frs'))->with('error', 'Kelas tidak tersedia untuk program studi Anda.');
        }
        $db = db_connect();
        if ($db->table('frs_detail')->where('mahasiswa_id', $profile['id'])->where('kelas_id', $classId)->countAllResults()) {
            return redirect()->to(site_url('mahasiswa/frs'))->with('error', 'Kelas tersebut sudah ada di FRS Anda.');
        }
        $current = $this->frsRows($profile['id'], $year, $semester);
        $sks = array_sum(array_column($current, 'sks'));
        if ($sks + (int) $class['sks'] > 24) {
            return redirect()->to(site_url('mahasiswa/frs'))->with('error', 'Batas pengambilan mata kuliah adalah 24 SKS per semester.');
        }
        $capacity = $db->table('frs_detail')->where('kelas_id', $classId)->countAllResults();
        if ($capacity >= (int) $class['kuota']) return redirect()->to(site_url('mahasiswa/frs'))->with('error', 'Kuota kelas sudah penuh.');
        $db->table('frs_detail')->insert(['mahasiswa_id' => $profile['id'], 'kelas_id' => $classId]);
        return redirect()->to(site_url('mahasiswa/frs'))->with('success', 'Mata kuliah ditambahkan ke FRS.');
    }

    public function removeClass(int $id)
    {
        $profile = $this->profileData();
        $item = db_connect()->table('frs_detail')->where('id', $id)->where('mahasiswa_id', $profile['id'])->get()->getRowArray();
        if (! $item) return redirect()->to(site_url('mahasiswa/frs'))->with('error', 'Mata kuliah tidak ditemukan pada FRS Anda.');
        if ($item['nilai_angka'] !== null) return redirect()->to(site_url('mahasiswa/frs'))->with('error', 'Mata kuliah yang sudah memiliki nilai tidak dapat dihapus.');
        db_connect()->table('frs_detail')->where('id', $id)->delete();
        return redirect()->to(site_url('mahasiswa/frs'))->with('success', 'Mata kuliah dihapus dari FRS.');
    }

    public function grades()
    {
        $profile = $this->profileData();
        return view('mahasiswa/grades', ['title' => 'Nilai dan IPK', 'profile' => $profile, 'grades' => $this->gradeRows($profile['id'])]);
    }

    public function printFrs()
    {
        $profile = $this->profileData();
        [$defaultYear, $defaultSemester] = $this->currentTerm();
        $year = (string) ($this->request->getGet('tahun_ajaran') ?: $defaultYear);
        $semester = (string) ($this->request->getGet('semester') ?: $defaultSemester);
        return view('mahasiswa/print-frs', ['profile' => $profile, 'year' => $year, 'semester' => $semester, 'rows' => $this->frsRows($profile['id'], $year, $semester)]);
    }

    public function printTranscript()
    {
        $profile = $this->profileData();
        return view('mahasiswa/print-transcript', ['profile' => $profile, 'grades' => $this->gradeRows($profile['id'])]);
    }

    private function profileData(): array
    {
        $row = db_connect()->table('mahasiswa m')->select('m.*,u.name,u.email,p.kode AS prodi_kode,p.nama AS prodi_name,w.nip AS wali_nip,wu.name AS dosen_wali')
            ->join('users u', 'u.id=m.user_id')->join('program_studi p', 'p.id=m.prodi_id')
            ->join('dosen w', 'w.id=m.dosen_wali_id', 'left')->join('users wu', 'wu.id=w.user_id', 'left')
            ->where('m.user_id', session()->get('user')['id'])->get()->getRowArray();
        return $row ?? throw PageNotFoundException::forPageNotFound('Profil mahasiswa belum dibuat oleh admin.');
    }

    private function currentTerm(): array
    {
        $year = (int) date('Y');
        if ((int) date('n') >= 7) return [$year . '/' . ($year + 1), 'Ganjil'];
        return [($year - 1) . '/' . $year, 'Genap'];
    }

    private function frsRows(int $studentId, string $year, string $semester): array
    {
        return db_connect()->table('frs_detail f')->select('f.id AS frs_id,f.nilai_angka,f.nilai_huruf,k.*,mk.kode,mk.nama AS mata_kuliah,mk.sks,u.name AS dosen_name')
            ->join('kelas k', 'k.id=f.kelas_id')->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')
            ->join('dosen d', 'd.id=k.dosen_id')->join('users u', 'u.id=d.user_id')
            ->where('f.mahasiswa_id', $studentId)->where('k.tahun_ajaran', $year)->where('k.semester', $semester)
            ->orderBy('mk.kode')->get()->getResultArray();
    }

    private function availableClasses(array $profile, string $year, string $semester): array
    {
        return db_connect()->table('kelas k')->select('k.*,mk.kode,mk.nama AS mata_kuliah,mk.sks,u.name AS dosen_name')
            ->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')->join('dosen d', 'd.id=k.dosen_id')->join('users u', 'u.id=d.user_id')
            ->join('frs_detail f', 'f.kelas_id=k.id AND f.mahasiswa_id=' . (int) $profile['id'], 'left')
            ->where('mk.prodi_id', $profile['prodi_id'])->where('k.tahun_ajaran', $year)->where('k.semester', $semester)
            ->where('f.id', null)->orderBy('mk.kode')->get()->getResultArray();
    }

    private function gradeRows(int $studentId): array
    {
        return db_connect()->table('frs_detail f')->select('f.nilai_angka,f.nilai_huruf,k.semester,k.tahun_ajaran,k.nama_kelas,mk.kode,mk.nama AS mata_kuliah,mk.sks,u.name AS dosen_name')
            ->join('kelas k', 'k.id=f.kelas_id')->join('mata_kuliah mk', 'mk.id=k.mata_kuliah_id')
            ->join('dosen d', 'd.id=k.dosen_id')->join('users u', 'u.id=d.user_id')
            ->where('f.mahasiswa_id', $studentId)->where('f.nilai_angka IS NOT NULL', null, false)
            ->orderBy('k.tahun_ajaran', 'DESC')->orderBy('k.semester')->orderBy('mk.kode')->get()->getResultArray();
    }
}
