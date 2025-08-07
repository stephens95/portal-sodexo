<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['role_name' => 'Admin'],
            ['role_name' => 'Manager'],
            ['role_name'  => 'User'],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
