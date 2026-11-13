<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['order_number','user_id','status','total_amount','shipping_name','shipping_phone','shipping_address'];
    protected $useTimestamps = false;
}
