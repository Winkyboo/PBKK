<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CategoryController extends BaseController
{
    public function index(): string
    {
        $categories = (new CategoryModel())->orderBy('name', 'ASC')->findAll();
        $productModel = new ProductModel();

        foreach ($categories as &$category) {
            $category['product_count'] = $productModel->countForCategory((int) $category['id']);
        }
        unset($category);

        return view('admin/categories/index', [
            'title'      => 'Kelola kategori',
            'active'     => 'admin-categories',
            'categories' => $categories,
        ]);
    }

    public function newForm(): string
    {
        return view('admin/categories/form', [
            'title'   => 'Tambah kategori',
            'active'  => 'admin-categories',
            'category'=> null,
            'errors'  => session()->getFlashdata('errors') ?? [],
            'values'  => session()->getFlashdata('old_values') ?? [],
        ]);
    }

    public function create()
    {
        $rules = $this->categoryRules();
        if (! $this->validate($rules)) {
            return $this->returnToForm(url_to('admin.categories.new'));
        }

        $model = new CategoryModel();
        $name = trim((string) $this->request->getPost('name'));
        if ($model->where('name', $name)->first() !== null) {
            return $this->returnToForm(url_to('admin.categories.new'), [
                'name' => 'Nama kategori tersebut sudah digunakan.',
            ]);
        }

        if (! $model->insert($this->categoryData())) {
            return $this->returnToForm(url_to('admin.categories.new'), $model->errors());
        }

        return redirect()->to(url_to('admin.categories.index'))
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $category = (new CategoryModel())->find($id);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        return view('admin/categories/form', [
            'title'    => 'Ubah kategori',
            'active'   => 'admin-categories',
            'category' => $category,
            'errors'   => session()->getFlashdata('errors') ?? [],
            'values'   => session()->getFlashdata('old_values') ?? [],
        ]);
    }

    public function update(int $id)
    {
        $model = new CategoryModel();
        $category = $model->find($id);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        if (! $this->validate($this->categoryRules())) {
            return $this->returnToForm(url_to('admin.categories.edit', $id));
        }

        $name = trim((string) $this->request->getPost('name'));
        $duplicate = $model->where('name', $name)->where('id !=', $id)->first();
        if ($duplicate !== null) {
            return $this->returnToForm(url_to('admin.categories.edit', $id), [
                'name' => 'Nama kategori tersebut sudah digunakan.',
            ]);
        }

        if (! $model->update($id, $this->categoryData())) {
            return $this->returnToForm(url_to('admin.categories.edit', $id), $model->errors());
        }

        return redirect()->to(url_to('admin.categories.index'))
            ->with('success', 'Perubahan kategori berhasil disimpan.');
    }

    public function delete(int $id)
    {
        $model = new CategoryModel();
        $category = $model->find($id);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        if ((new ProductModel())->countForCategory($id) > 0) {
            return redirect()->to(url_to('admin.categories.index'))
                ->with('error', 'Kategori masih digunakan produk. Pindahkan produk ke kategori lain sebelum menghapusnya.');
        }

        if (! $model->delete($id)) {
            return redirect()->to(url_to('admin.categories.index'))
                ->with('error', 'Kategori gagal dihapus.');
        }

        return redirect()->to(url_to('admin.categories.index'))
            ->with('success', 'Kategori berhasil dihapus.');
    }

    private function categoryRules(): array
    {
        return [
            'name'   => 'required|min_length[2]|max_length[100]',
            'detail' => 'required|min_length[5]|max_length[180]',
            'mark'   => 'required|max_length[5]',
        ];
    }

    private function categoryData(): array
    {
        return [
            'name'   => trim((string) $this->request->getPost('name')),
            'detail' => trim((string) $this->request->getPost('detail')),
            'mark'   => trim((string) $this->request->getPost('mark')),
        ];
    }

    private function returnToForm(string $url, array $errors = [])
    {
        if ($errors === []) {
            $errors = $this->validator->getErrors();
        }

        return redirect()->to($url)
            ->with('errors', $errors)
            ->with('old_values', $this->request->getPost());
    }
}
