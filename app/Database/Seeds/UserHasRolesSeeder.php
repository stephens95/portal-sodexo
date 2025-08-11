<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserHasRolesSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('user_has_roles')->truncate();
        $users = $this->db->table('users')->get()->getResultArray();
        $roles = $this->db->table('roles')->get()->getResultArray();
        $this->db->table('user_has_roles')->insert([
            'user_id' => $users[0]['user_id'],
            'role_id' => $roles[0]['role_id']
        ]);
    }
}
