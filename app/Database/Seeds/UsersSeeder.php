<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('users')->truncate();
        $this->db->table('users')->insert([
            'email' => 'it5@amt.co.id',
            'password' => password_hash('1234', PASSWORD_DEFAULT),
            'name' => 'Stephen',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
