<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudios extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'hourly_rate' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'capacity'    => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'photo'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('studios');
    }

    public function down(): void
    {
        $this->forge->dropTable('studios', true);
    }
}
