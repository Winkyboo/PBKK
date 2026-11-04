<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipments extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'category'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'stock'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'rental_price' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('equipments');
    }

    public function down(): void
    {
        $this->forge->dropTable('equipments', true);
    }
}
