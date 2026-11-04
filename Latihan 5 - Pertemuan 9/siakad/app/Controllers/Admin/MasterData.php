<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;
use App\Models\KelasModel;
use App\Models\MahasiswaModel;
use App\Models\MataKuliahModel;
use App\Models\ProgramStudiModel;
use App\Models\UserModel;

class MasterData extends BaseController
{
    private array $entities = [
        'prodi' => ['title' => 'Program Studi', 'model' => ProgramStudiModel::class, 'table' => 'program_studi', 'columns' => ['kode' => 'Kode', 'nama' => 'Nama program studi'], 'fields' => [
            ['key' => 'kode', 'label' => 'Kode', 'type' => 'text'], ['key' => 'nama', 'label' => 'Nama program studi', 'type' => 'text'],
        ]],
        'mahasiswa' => ['title' => 'Mahasiswa', 'model' => MahasiswaModel::class, 'table' => 'mahasiswa', 'columns' => ['nim' => 'NIM', 'name' => 'Nama', 'prodi_name' => 'Program studi', 'angkatan' => 'Angkatan'], 'fields' => [
            ['key' => 'nim', 'label' => 'NIM', 'type' => 'text'], ['key' => 'name', 'label' => 'Nama lengkap', 'type' => 'text'], ['key' => 'email', 'label' => 'Email akun', 'type' => 'email'], ['key' => 'prodi_id', 'label' => 'Program studi', 'type' => 'prodi'], ['key' => 'angkatan', 'label' => 'Tahun angkatan', 'type' => 'number'], ['key' => 'dosen_wali_id', 'label' => 'Dosen wali', 'type' => 'dosen', 'optional' => true],
        ]],
        'dosen' => ['title' => 'Dosen', 'model' => DosenModel::class, 'table' => 'dosen', 'columns' => ['nip' => 'NIP', 'name' => 'Nama', 'email' => 'Email', 'prodi_name' => 'Program studi'], 'fields' => [
            ['key' => 'nip', 'label' => 'NIP', 'type' => 'text'], ['key' => 'name', 'label' => 'Nama lengkap', 'type' => 'text'], ['key' => 'email', 'label' => 'Email akun', 'type' => 'email'], ['key' => 'prodi_id', 'label' => 'Program studi', 'type' => 'prodi'],
        ]],
        'matakuliah' => ['title' => 'Mata Kuliah', 'model' => MataKuliahModel::class, 'table' => 'mata_kuliah', 'columns' => ['kode' => 'Kode', 'nama' => 'Nama mata kuliah', 'sks' => 'SKS', 'prodi_name' => 'Program studi'], 'fields' => [
            ['key' => 'kode', 'label' => 'Kode mata kuliah', 'type' => 'text'], ['key' => 'nama', 'label' => 'Nama mata kuliah', 'type' => 'text'], ['key' => 'sks', 'label' => 'Jumlah SKS', 'type' => 'number'], ['key' => 'prodi_id', 'label' => 'Program studi', 'type' => 'prodi'],
        ]],
        'kelas' => ['title' => 'Kelas Kuliah', 'model' => KelasModel::class, 'table' => 'kelas', 'columns' => ['mata_kuliah' => 'Mata kuliah', 'nama_kelas' => 'Kelas', 'dosen_name' => 'Dosen pengampu', 'semester' => 'Semester', 'tahun_ajaran' => 'Tahun ajaran', 'kuota' => 'Kuota'], 'fields' => [
            ['key' => 'mata_kuliah_id', 'label' => 'Mata kuliah', 'type' => 'matakuliah'], ['key' => 'dosen_id', 'label' => 'Dosen pengampu', 'type' => 'dosen'], ['key' => 'semester', 'label' => 'Semester', 'type' => 'semester'], ['key' => 'tahun_ajaran', 'label' => 'Tahun ajaran (contoh 2026/2027)', 'type' => 'text'], ['key' => 'nama_kelas', 'label' => 'Nama kelas', 'type' => 'text'], ['key' => 'kuota', 'label' => 'Kuota mahasiswa', 'type' => 'number'],
        ]],
    ];

    public function index(string $entity)
    {
        $config = $this->config($entity);
        $builder = db_connect()->table($config['table'] . ' t');
        $builder->select('t.*');
        if ($entity === 'mahasiswa') $builder->select('u.name,u.email,p.nama AS prodi_name', false, false)->join('users u', 'u.id=t.user_id')->join('program_studi p', 'p.id=t.prodi_id');
        if ($entity === 'dosen') $builder->select('u.name,u.email,p.nama AS prodi_name', false, false)->join('users u', 'u.id=t.user_id')->join('program_studi p', 'p.id=t.prodi_id');
        if ($entity === 'matakuliah') $builder->select('p.nama AS prodi_name', false, false)->join('program_studi p', 'p.id=t.prodi_id');
        if ($entity === 'kelas') $builder->select('mk.nama AS mata_kuliah, u.name AS dosen_name', false, false)->join('mata_kuliah mk', 'mk.id=t.mata_kuliah_id')->join('dosen d', 'd.id=t.dosen_id')->join('users u', 'u.id=d.user_id');
        $rows = $builder->orderBy('t.id', 'DESC')->get()->getResultArray();
        return view('admin/master/index', ['title' => $config['title'], 'entity' => $entity, 'config' => $config, 'rows' => $rows]);
    }

    public function form(string $entity, ?int $id = null)
    {
        $config = $this->config($entity);
        $record = $id ? model($config['model'])->find($id) : null;
        if ($id && ! $record) throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan.');
        if ($record && in_array($entity, ['mahasiswa', 'dosen'], true)) {
            $user = model(UserModel::class)->find($record['user_id']);
            $record = array_merge($record, ['name' => $user['name'], 'email' => $user['email']]);
        }
        return view('admin/master/form', ['title' => ($id ? 'Ubah ' : 'Tambah ') . $config['title'], 'entity' => $entity, 'config' => $config, 'record' => $record, 'options' => $this->options()]);
    }

    public function save(string $entity, ?int $id = null)
    {
        $config = $this->config($entity);
        $rules = $this->rules($entity);
        if (! $this->validate($rules)) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $post = $this->request->getPost();
        $model = model($config['model']);
        $data = [];
        foreach ($config['fields'] as $field) {
            $key = $field['key'];
            if (in_array($key, ['name', 'email'], true) && in_array($entity, ['mahasiswa', 'dosen'], true)) continue;
            $value = $post[$key] ?? null;
            $data[$key] = ($value === '' && ($field['optional'] ?? false)) ? null : $value;
        }

        if (in_array($entity, ['mahasiswa', 'dosen'], true)) {
            $profile = $id ? $model->find($id) : null;
            $role = $entity;
            $userData = ['name' => trim((string) $post['name']), 'email' => strtolower(trim((string) $post['email']))];
            if ($profile) {
                model(UserModel::class)->update($profile['user_id'], $userData);
                $model->update($id, $data);
            } else {
                $userData['role'] = $role;
                $userData['password_hash'] = password_hash($entity === 'dosen' ? 'Dosen123!' : 'Mahasiswa123!', PASSWORD_DEFAULT);
                $userId = model(UserModel::class)->insert($userData);
                $data['user_id'] = (int) $userId;
                $model->insert($data);
            }
        } elseif ($id) {
            $model->update($id, $data);
        } else {
            $model->insert($data);
        }
        return redirect()->to(site_url('admin/data/' . $entity))->with('success', $config['title'] . ' berhasil disimpan.');
    }

    public function delete(string $entity, int $id)
    {
        $config = $this->config($entity);
        $row = model($config['model'])->find($id);
        if (! $row) return redirect()->to(site_url('admin/data/' . $entity));
        $db = db_connect();
        $blocked = match ($entity) {
            'mahasiswa' => $db->table('frs_detail')->where('mahasiswa_id', $id)->countAllResults() > 0,
            'dosen' => $db->table('kelas')->where('dosen_id', $id)->countAllResults() > 0 || $db->table('mahasiswa')->where('dosen_wali_id', $id)->countAllResults() > 0,
            'prodi' => $db->table('mahasiswa')->where('prodi_id', $id)->countAllResults() > 0 || $db->table('dosen')->where('prodi_id', $id)->countAllResults() > 0 || $db->table('mata_kuliah')->where('prodi_id', $id)->countAllResults() > 0,
            'matakuliah' => $db->table('kelas')->where('mata_kuliah_id', $id)->countAllResults() > 0,
            'kelas' => $db->table('frs_detail')->where('kelas_id', $id)->countAllResults() > 0,
            default => false,
        };
        if ($blocked) return redirect()->to(site_url('admin/data/' . $entity))->with('error', 'Data ini masih digunakan. Hapus atau pindahkan relasinya terlebih dahulu.');
        model($config['model'])->delete($id);
        if (in_array($entity, ['mahasiswa', 'dosen'], true)) model(UserModel::class)->delete($row['user_id']);
        return redirect()->to(site_url('admin/data/' . $entity))->with('success', $config['title'] . ' berhasil dihapus.');
    }

    private function config(string $entity): array
    {
        if (! isset($this->entities[$entity])) throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis data tidak ditemukan.');
        return $this->entities[$entity];
    }

    private function rules(string $entity): array
    {
        $rules = [];
        foreach ($this->config($entity)['fields'] as $field) {
            if ($field['type'] === 'email') $rules[$field['key']] = 'required|valid_email|max_length[150]';
            elseif (($field['optional'] ?? false)) $rules[$field['key']] = 'permit_empty|integer';
            elseif ($field['type'] === 'number') $rules[$field['key']] = 'required|integer|greater_than[0]';
            elseif (in_array($field['type'], ['prodi', 'dosen', 'matakuliah', 'semester'], true)) $rules[$field['key']] = 'required';
            else $rules[$field['key']] = 'required|max_length[150]';
        }
        return $rules;
    }

    private function options(): array
    {
        $db = db_connect();
        return [
            'prodi' => $db->table('program_studi')->orderBy('nama')->get()->getResultArray(),
            'dosen' => $db->table('dosen d')->select('d.id,u.name,u.email')->join('users u', 'u.id=d.user_id')->orderBy('u.name')->get()->getResultArray(),
            'matakuliah' => $db->table('mata_kuliah')->orderBy('nama')->get()->getResultArray(),
        ];
    }
}
