<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'phone',
        'shipping_address',
        'city',
        'postal_code',
        'shipping_method',
        'shipping_cost',
        'subtotal',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'tracking_number',
    ];

    public function forCustomer(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('id', 'DESC')->findAll();
    }

    public function forAdmin(): array
    {
        return $this->orderBy('id', 'DESC')->findAll(100);
    }
}
