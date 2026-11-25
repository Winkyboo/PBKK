<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'unit_price',
        'quantity',
        'line_total',
    ];

    public function forOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)->orderBy('id', 'ASC')->findAll();
    }
}
