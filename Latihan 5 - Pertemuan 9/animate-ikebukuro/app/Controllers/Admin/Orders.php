<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Orders extends BaseController
{
    public function index()
    {
        $orders=db_connect()->table('orders o')->select('o.*,u.name AS customer_name,u.email')->join('users u','u.id=o.user_id')->orderBy('o.id','DESC')->get()->getResultArray();
        $items=[];
        if ($orders) $items=db_connect()->table('order_items')->whereIn('order_id',array_column($orders,'id'))->orderBy('id')->get()->getResultArray();
        $grouped=[]; foreach($items as $item) $grouped[$item['order_id']][]=$item;
        return view('admin/orders/index',['title'=>'Pesanan','orders'=>$orders,'itemsByOrder'=>$grouped]);
    }
    public function updateStatus(int $id)
    {
        $order=model(OrderModel::class)->find($id);
        if (!$order) throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        $status=(string)$this->request->getPost('status');
        if (!in_array($status,['Menunggu','Diproses','Dikirim','Selesai','Dibatalkan'],true)) return redirect()->back()->with('error','Status tidak valid.');
        $transitions = [
            'Menunggu' => ['Diproses', 'Dibatalkan'],
            'Diproses' => ['Dikirim', 'Dibatalkan'],
            'Dikirim' => ['Selesai'],
            'Selesai' => [],
            'Dibatalkan' => [],
        ];
        if ($status !== $order['status'] && ! in_array($status, $transitions[$order['status']] ?? [], true)) return redirect()->back()->with('error','Perubahan status pesanan tidak valid.');
        if ($status==='Dibatalkan' && $order['status']!=='Dibatalkan') {
            $lines=db_connect()->table('order_items')->where('order_id',$id)->get()->getResultArray();
            foreach($lines as $line) db_connect()->table('products')->set('stock','stock + '.(int)$line['quantity'],false)->where('id',$line['product_id'])->update();
        }
        model(OrderModel::class)->update($id,['status'=>$status]);
        return redirect()->to(site_url('admin/pesanan'))->with('success','Status pesanan diperbarui.');
    }
}
