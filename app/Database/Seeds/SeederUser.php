<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeederUser extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username' => 'Admin',
                'nama' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role_id' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('users')->insertBatch($users);
    }
}

// php spark db:seed SeederUser