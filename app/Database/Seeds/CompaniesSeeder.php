<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'PT Maju Jaya'],
            ['name' => 'CV Sukses Abadi'],
            ['name' => 'UD Makmur Sentosa'],
        ];

        $this->db->table('companies')->insertBatch($data);
    }
}
