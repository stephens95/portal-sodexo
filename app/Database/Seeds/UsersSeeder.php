<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID dari roles dan companies
        $roles = $this->db->table('roles')->get()->getResultArray();
        $companies = $this->db->table('companies')->get()->getResultArray();

        $data = [
            [
                'name'       => 'John Doe',
                'email'      => 'john@example.com',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role_id'    => $roles[0]['id'],
                'company_id' => $companies[0]['id'],
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name'       => 'Jane Smith',
                'email'      => 'jane@example.com',
                'password'   => password_hash('123456', PASSWORD_DEFAULT),
                'role_id'    => $roles[1]['id'],
                'company_id' => $companies[1]['id'],
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
