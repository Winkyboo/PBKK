<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    public function index()
    {
        $rows=db_connect()->table('categories c')->select('c.*,COUNT(p.id) AS product_count')->join('products p','p.category_id=c.id','left')->groupBy('c.id,c.name,c.description')->orderBy('c.name')->get()->getResultArray();
        return view('admin/categories/index',['title'=>'Kategori','categories'=>$rows]);
    }
    public function create()
    {
        if (!$this->validate(['name'=>'required|max_length[100]','description'=>'permit_empty|max_length[300]'])) return redirect()->back()->withInput()->with('errors',$this->validator->getErrors());
        model(CategoryModel::class)->insert(['name'=>trim((string)$this->request->getPost('name')),'description'=>trim((string)$this->request->getPost('description'))]);
        return redirect()->to(site_url('admin/kategori'))->with('success','Kategori ditambahkan.');
    }
    public function delete(int $id)
    {
        if (db_connect()->table('products')->where('category_id',$id)->countAllResults()) return redirect()->to(site_url('admin/kategori'))->with('error','Kategori masih digunakan oleh produk.');
        model(CategoryModel::class)->delete($id);
        return redirect()->to(site_url('admin/kategori'))->with('success','Kategori dihapus.');
    }
}
