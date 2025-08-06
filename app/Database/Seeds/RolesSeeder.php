<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Admin'],
            ['name' => 'Manager'],
            ['name' => 'User'],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
