<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Products extends BaseController
{
    public function index()
    {
        $rows = db_connect()->table('products p')->select('p.*,c.name AS category_name')->join('categories c','c.id=p.category_id')->orderBy('p.id','DESC')->get()->getResultArray();
        return view('admin/products/index', ['title' => 'Produk', 'products' => $rows]);
    }
    public function new()
    {
        return view('admin/products/form', ['title' => 'Tambah produk', 'product' => null, 'categories' => model(CategoryModel::class)->orderBy('name')->findAll()]);
    }
    public function create()
    {
        if (! $this->validate($this->rules())) return redirect()->back()->withInput()->with('errors',$this->validator->getErrors());
        $image = $this->saveImage();
        if ($image === false) return redirect()->back()->withInput()->with('error','Gambar harus JPG, PNG, atau WebP maksimal 2 MB.');
        model(ProductModel::class)->insert($this->payload($image));
        return redirect()->to(site_url('admin/produk'))->with('success','Produk berhasil ditambahkan.');
    }
    public function edit(int $id)
    {
        return view('admin/products/form', ['title'=>'Ubah produk','product'=>$this->find($id),'categories'=>model(CategoryModel::class)->orderBy('name')->findAll()]);
    }
    public function update(int $id)
    {
        $product=$this->find($id);
        if (! $this->validate($this->rules())) return redirect()->back()->withInput()->with('errors',$this->validator->getErrors());
        $image=$this->saveImage();
        if ($image===false) return redirect()->back()->withInput()->with('error','Gambar harus JPG, PNG, atau WebP maksimal 2 MB.');
        model(ProductModel::class)->update($id,$this->payload($image ?? $product['image']));
        if ($image && $product['image']) @unlink(FCPATH.$product['image']);
        return redirect()->to(site_url('admin/produk'))->with('success','Produk berhasil diperbarui.');
    }
    public function delete(int $id)
    {
        $product=$this->find($id);
        if (db_connect()->table('order_items')->where('product_id',$id)->countAllResults()) return redirect()->to(site_url('admin/produk'))->with('error','Produk sudah masuk riwayat pesanan. Ubah stok menjadi 0 agar tidak dijual.');
        model(ProductModel::class)->delete($id);
        if ($product['image']) @unlink(FCPATH.$product['image']);
        return redirect()->to(site_url('admin/produk'))->with('success','Produk dihapus.');
    }
    private function find(int $id): array
    {
        return model(ProductModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
    }
    private function rules(): array
    {
        return ['name'=>'required|max_length[160]','category_id'=>'required|integer','description'=>'permit_empty|max_length[1200]','price'=>'required|decimal|greater_than_equal_to[0]','stock'=>'required|integer|greater_than_equal_to[0]'];
    }
    private function payload(?string $image): array
    {
        return ['category_id'=>(int)$this->request->getPost('category_id'),'name'=>trim((string)$this->request->getPost('name')),'description'=>trim((string)$this->request->getPost('description')),'price'=>(float)$this->request->getPost('price'),'stock'=>(int)$this->request->getPost('stock'),'image'=>$image,'is_featured'=>$this->request->getPost('is_featured')?1:0];
    }
    private function saveImage()
    {
        $file=$this->request->getFile('image');
        if (!$file || $file->getError()===UPLOAD_ERR_NO_FILE) return null;
        if (!$file->isValid() || !in_array($file->getMimeType(),['image/jpeg','image/png','image/webp'],true) || $file->getSizeByUnit('mb')>2) return false;
        $dir=FCPATH.'uploads/products';
        if (!is_dir($dir)) mkdir($dir,0775,true);
        $name=$file->getRandomName(); $file->move($dir,$name);
        return 'uploads/products/'.$name;
    }
}
