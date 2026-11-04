<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EquipmentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Equipments extends BaseController
{
    public function index(): string
    {
        return view('admin/equipments/index', ['equipments' => model(EquipmentModel::class)->orderBy('name', 'ASC')->findAll()]);
    }

    public function createForm(): string
    {
        return view('admin/equipments/form', ['equipment' => null]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        model(EquipmentModel::class)->insert($this->payload());

        return redirect()->to('/admin/alat')->with('success', 'Alat ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/equipments/form', ['equipment' => $this->find($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->find($id);
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        model(EquipmentModel::class)->update($id, $this->payload());

        return redirect()->to('/admin/alat')->with('success', 'Data alat diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $this->find($id);
        model(EquipmentModel::class)->delete($id);

        return redirect()->to('/admin/alat')->with('success', 'Alat dihapus.');
    }

    private function rules(): array
    {
        return [
            'name'         => ['label' => 'Nama alat', 'rules' => 'required|max_length[100]'],
            'category'     => ['label' => 'Kategori', 'rules' => 'required|max_length[50]'],
            'stock'        => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than_equal_to[1000]'],
            'rental_price' => ['label' => 'Harga sewa', 'rules' => 'required|is_natural|less_than_equal_to[100000000]'],
        ];
    }

    private function payload(): array
    {
        return [
            'name'         => trim((string) $this->request->getPost('name')),
            'category'     => trim((string) $this->request->getPost('category')),
            'stock'        => (int) $this->request->getPost('stock'),
            'rental_price' => (int) $this->request->getPost('rental_price'),
        ];
    }

    private function find(int $id): array
    {
        return model(EquipmentModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
