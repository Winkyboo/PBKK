<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\StudioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Studios extends BaseController
{
    public function index(): string
    {
        return view('admin/studios/index', ['studios' => model(StudioModel::class)->orderBy('name', 'ASC')->findAll()]);
    }

    public function createForm(): string
    {
        return view('admin/studios/form', ['studio' => null]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $photo = $this->savePhoto();
        model(StudioModel::class)->insert($this->payload($photo));

        return redirect()->to('/admin/studio')->with('success', 'Studio ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/studios/form', ['studio' => $this->find($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $studio = $this->find($id);
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $photo = $this->savePhoto();
        if ($photo !== null) {
            $this->deletePhoto($studio['photo']);
        }
        model(StudioModel::class)->update($id, $this->payload($photo ?? $studio['photo']));

        return redirect()->to('/admin/studio')->with('success', 'Data studio diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $studio = $this->find($id);
        if (model(BookingModel::class)->where('studio_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/studio')->with('error', 'Studio ini sudah punya riwayat booking. Nonaktifkan saja lewat menu ubah.');
        }

        $this->deletePhoto($studio['photo']);
        model(StudioModel::class)->delete($id);

        return redirect()->to('/admin/studio')->with('success', 'Studio dihapus.');
    }

    private function rules(): array
    {
        $rules = [
            'name'        => ['label' => 'Nama studio', 'rules' => 'required|max_length[100]'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[500]'],
            'hourly_rate' => ['label' => 'Tarif per jam', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[100000000]'],
            'capacity'    => ['label' => 'Kapasitas', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[200]'],
        ];
        $file = $this->request->getFile('photo');
        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['photo'] = [
                'label' => 'Foto studio',
                'rules' => 'uploaded[photo]|max_size[photo,2048]|is_image[photo]|ext_in[photo,jpg,jpeg,png,webp]|mime_in[photo,image/jpg,image/jpeg,image/png,image/webp]',
            ];
        }

        return $rules;
    }

    private function payload(?string $photo): array
    {
        $desc = trim((string) $this->request->getPost('description'));

        return [
            'name'        => trim((string) $this->request->getPost('name')),
            'description' => $desc === '' ? null : $desc,
            'hourly_rate' => (int) $this->request->getPost('hourly_rate'),
            'capacity'    => (int) $this->request->getPost('capacity'),
            'photo'       => $photo,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    /** Menyimpan foto ke public/uploads/studios dengan nama acak. Mengembalikan nama file atau null. */
    private function savePhoto(): ?string
    {
        $file = $this->request->getFile('photo');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $name = $file->getRandomName();
        $file->move(FCPATH . 'uploads/studios', $name);

        return $name;
    }

    private function deletePhoto(?string $name): void
    {
        if ($name) {
            $path = FCPATH . 'uploads/studios/' . basename($name);
            is_file($path) && unlink($path);
        }
    }

    private function find(int $id): array
    {
        return model(StudioModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
