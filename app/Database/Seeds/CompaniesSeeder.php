<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['company_name' => 'PT Maju Jaya'],
            ['company_name' => 'CV Sukses Abadi'],
            ['company_name' => 'UD Makmur Sentosa'],
        ];

        $this->db->table('companies')->insertBatch($data);
    }
}
