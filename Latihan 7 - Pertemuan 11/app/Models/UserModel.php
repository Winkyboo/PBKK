<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'password_hash',
        'role',
        'is_active',
    ];

    protected $validationRules = [
        'name'          => 'required|min_length[3]|max_length[100]',
        'email'         => 'required|valid_email|max_length[150]',
        'password_hash' => 'required|max_length[255]',
        'role'          => 'required|in_list[admin,operator,customer]',
        'is_active'     => 'required|in_list[0,1]',
    ];

    public function findActiveByEmail(string $email): ?array
    {
        $user = $this->where('email', $email)
            ->where('is_active', 1)
            ->first();

        return is_array($user) ? $user : null;
    }
}
