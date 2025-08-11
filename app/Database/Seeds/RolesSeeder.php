<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('roles')->truncate();
        $data = [
            ['role_name' => 'Admin'],
            ['role_name'  => 'User'],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
