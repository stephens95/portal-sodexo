<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserHasBuyersSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('user_has_buyers')->truncate();
        $users  = $this->db->table('users')->get()->getResultArray();
        $buyers = $this->db->table('buyers')->get()->getResultArray();
        $this->db->table('user_has_buyers')->insert([
            'user_id'  => $users[0]['user_id'],
            'buyer_id' => $buyers[0]['buyer_id']
        ]);
    }
}
