<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookings extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'studio_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'booking_date'  => ['type' => 'DATE'],
            'start_hour'    => ['type' => 'INT', 'constraint' => 11],
            'end_hour'      => ['type' => 'INT', 'constraint' => 11],
            'total_price'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'menunggu'],
            'notes'         => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'payment_proof' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['studio_id', 'booking_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
        $this->forge->addForeignKey('studio_id', 'studios', 'id', 'NO ACTION', 'NO ACTION');
        $this->forge->createTable('bookings');
    }

    public function down(): void
    {
        $this->forge->dropTable('bookings', true);
    }
}
