<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('roles')->truncate();
        $data = [
            ['role_name' => 'Admin01'],
            ['role_name' => 'Admin02'],
            ['role_name' => 'User01'],
            ['role_name' => 'User02'],
            ['role_name' => 'User03'],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
