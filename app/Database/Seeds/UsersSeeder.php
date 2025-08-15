<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('users')->insert([
        //     'email' => 'it5@amt.co.id',
        //     'password' => password_hash('1234', PASSWORD_DEFAULT),
        //     'name' => 'Stephen',
        //     'created_at' => date('Y-m-d H:i:s')
        // ]);

        $userModel = new UserModel();
        $userModel->insert([
            'email'       => 'it5@amt.co.id',
            'password'    => password_hash('1234', PASSWORD_DEFAULT),
            'name'        => 'Stephen',
            'last_login'  => date('Y-m-d H:i:s'),
            'created_at'  => date('Y-m-d H:i:s')
        ]);
    }
}
