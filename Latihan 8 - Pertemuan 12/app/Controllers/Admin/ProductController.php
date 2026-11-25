<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;

class ProductController extends BaseController
{
    private const IMAGE_RULES = 'uploaded[image]|max_size[image,2048]|ext_in[image,jpg,jpeg,png,webp]|mime_in[image,image/jpeg,image/png,image/webp]|is_image[image]';

    public function index(): string
    {
        $model = new ProductModel();

        return view('admin/products/index', [
            'title'    => 'Kelola produk',
            'active'   => 'admin-products',
            'products' => $model->adminList(),
            'pager'    => $model->pager,
        ]);
    }

    public function newForm(): string
    {
        return view('admin/products/form', [
            'title'      => 'Tambah produk',
            'active'     => 'admin-products',
            'product'    => null,
            'categories' => (new CategoryModel())->orderBy('name', 'ASC')->findAll(),
            'errors'     => session()->getFlashdata('errors') ?? [],
            'values'     => session()->getFlashdata('old_values') ?? [],
        ]);
    }

    public function create()
    {
        $rules = $this->productRules();
        $rules['image'] = self::IMAGE_RULES;

        if (! $this->validate($rules)) {
            return $this->returnToForm(url_to('admin.products.new'));
        }

        $categoryId = (int) $this->request->getPost('category_id');
        if ((new CategoryModel())->find($categoryId) === null) {
            return redirect()->back()
                ->with('errors', ['category_id' => 'Kategori yang dipilih tidak ditemukan.'])
                ->with('old_values', $this->request->getPost());
        }

        $image = $this->storeImage($this->request->getFile('image'));
        if ($image === null) {
            return redirect()->back()->with('error', 'Gambar produk gagal disimpan. Coba pilih file lain.');
        }

        $model = new ProductModel();
        $id = $model->insert($this->productData($image));
        if ($id === false) {
            $this->deleteUploadedImage($image);

            return $this->returnToForm(url_to('admin.products.new'), $model->errors());
        }

        return redirect()->to(url_to('admin.products.index'))
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        $product = (new ProductModel())->find($id);
        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        return view('admin/products/form', [
            'title'      => 'Ubah produk',
            'active'     => 'admin-products',
            'product'    => $product,
            'categories' => (new CategoryModel())->orderBy('name', 'ASC')->findAll(),
            'errors'     => session()->getFlashdata('errors') ?? [],
            'values'     => session()->getFlashdata('old_values') ?? [],
        ]);
    }

    public function update(int $id)
    {
        $model = new ProductModel();
        $product = $model->find($id);
        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        $file = $this->request->getFile('image');
        $hasNewImage = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $rules = $this->productRules();
        if ($hasNewImage) {
            $rules['image'] = self::IMAGE_RULES;
        }

        if (! $this->validate($rules)) {
            return $this->returnToForm(url_to('admin.products.edit', $id));
        }

        $categoryId = (int) $this->request->getPost('category_id');
        if ((new CategoryModel())->find($categoryId) === null) {
            return redirect()->back()
                ->with('errors', ['category_id' => 'Kategori yang dipilih tidak ditemukan.'])
                ->with('old_values', $this->request->getPost());
        }

        $image = $product['image'];
        if ($hasNewImage) {
            $image = $this->storeImage($file);
            if ($image === null) {
                return redirect()->back()->with('error', 'Gambar produk gagal disimpan. Coba pilih file lain.');
            }
        }

        if (! $model->update($id, $this->productData($image))) {
            if ($hasNewImage) {
                $this->deleteUploadedImage($image);
            }

            return $this->returnToForm(url_to('admin.products.edit', $id), $model->errors());
        }

        $hasOrderHistory = (new OrderItemModel())->where('product_id', $id)->countAllResults() > 0;
        if ($hasNewImage && ! $hasOrderHistory) {
            $this->deleteUploadedImage($product['image']);
        }

        return redirect()->to(url_to('admin.products.index'))
            ->with('success', 'Perubahan produk berhasil disimpan.');
    }

    public function delete(int $id)
    {
        $model = new ProductModel();
        $product = $model->find($id);
        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        $hasOrderHistory = (new OrderItemModel())->where('product_id', $id)->countAllResults() > 0;

        if (! $model->delete($id)) {
            return redirect()->to(url_to('admin.products.index'))
                ->with('error', 'Produk gagal dihapus.');
        }

        if (! $hasOrderHistory) {
            $this->deleteUploadedImage($product['image']);
        }

        return redirect()->to(url_to('admin.products.index'))
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function productRules(): array
    {
        return [
            'name'        => 'required|min_length[3]|max_length[120]',
            'category_id' => 'required|is_natural_no_zero',
            'description' => 'required|min_length[10]|max_length[1000]',
            'price'       => 'required|decimal|greater_than[0]',
            'stock'       => 'required|integer|greater_than_equal_to[0]',
            'status'      => 'required|in_list[active,inactive]',
        ];
    }

    private function productData(string $image): array
    {
        return [
            'name'        => trim((string) $this->request->getPost('name')),
            'category_id' => (int) $this->request->getPost('category_id'),
            'description' => trim((string) $this->request->getPost('description')),
            'price'       => trim((string) $this->request->getPost('price')),
            'stock'       => (int) $this->request->getPost('stock'),
            'image'       => $image,
            'status'      => (string) $this->request->getPost('status'),
        ];
    }

    private function storeImage(UploadedFile $file): ?string
    {
        $directory = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products';
        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            return null;
        }

        $filename = $file->getRandomName();
        try {
            $file->move($directory, $filename);
        } catch (\Throwable $exception) {
            return null;
        }

        return 'uploads/products/' . $filename;
    }

    private function deleteUploadedImage(string $image): void
    {
        if (! str_starts_with($image, 'uploads/products/')) {
            return;
        }

        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . basename($image);
        if (is_file($path)) {
            unlink($path);
        }
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
