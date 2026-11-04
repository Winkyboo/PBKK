<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/** Data awal: dua akun demo serta contoh studio dan alat. Aman dijalankan sekali pada database kosong. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('users')->countAllResults() === 0) {
            $this->db->table('users')->insertBatch([
                ['name' => 'Admin Studio', 'email' => 'admin@studio.test', 'password_hash' => password_hash('Admin123!', PASSWORD_DEFAULT), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Pelanggan Contoh', 'email' => 'pelanggan@studio.test', 'password_hash' => password_hash('Pelanggan123!', PASSWORD_DEFAULT), 'role' => 'pelanggan', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if ($this->db->table('studios')->countAllResults() === 0) {
            $this->db->table('studios')->insertBatch([
                ['name' => 'Studio A', 'description' => 'Ruang latihan band. Data contoh untuk demo.', 'hourly_rate' => 75000, 'capacity' => 6, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Studio B', 'description' => 'Ruang latihan kecil untuk duo atau trio. Data contoh untuk demo.', 'hourly_rate' => 50000, 'capacity' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Studio Rekaman', 'description' => 'Ruang rekaman vokal dan instrumen. Data contoh untuk demo.', 'hourly_rate' => 150000, 'capacity' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if ($this->db->table('equipments')->countAllResults() === 0) {
            $this->db->table('equipments')->insertBatch([
                ['name' => 'Gitar Elektrik', 'category' => 'Gitar', 'stock' => 4, 'rental_price' => 25000, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Bass Elektrik', 'category' => 'Bass', 'stock' => 2, 'rental_price' => 25000, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Mikrofon Vokal', 'category' => 'Mikrofon', 'stock' => 6, 'rental_price' => 10000, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Keyboard 61 Tuts', 'category' => 'Keyboard', 'stock' => 2, 'rental_price' => 30000, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }
}
