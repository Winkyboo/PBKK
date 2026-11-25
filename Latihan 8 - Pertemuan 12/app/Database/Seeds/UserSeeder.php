<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'     => 'Admin ShopEase',
                'email'    => 'admin@shopease.test',
                'password' => 'Admin123!',
                'role'     => 'admin',
            ],
            [
                'name'     => 'Operator ShopEase',
                'email'    => 'operator@shopease.test',
                'password' => 'Operator123!',
                'role'     => 'operator',
            ],
            [
                'name'     => 'Customer ShopEase',
                'email'    => 'customer@shopease.test',
                'password' => 'Customer123!',
                'role'     => 'customer',
            ],
        ];

        $model = new UserModel();

        foreach ($users as $user) {
            if ($model->where('email', $user['email'])->first() !== null) {
                continue;
            }

            $model->insert([
                'name'          => $user['name'],
                'email'         => $user['email'],
                'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
                'role'          => $user['role'],
                'is_active'     => 1,
            ]);
        }
    }
}
