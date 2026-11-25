<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrders extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'order_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'customer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'shipping_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'shipping_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'shipping_cost' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'payment_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'unpaid',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
            ],
            'tracking_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_number');
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'NO ACTION');
        $this->forge->createTable('orders', true);
    }

    public function down()
    {
        $this->forge->dropTable('orders', true);
    }
}
