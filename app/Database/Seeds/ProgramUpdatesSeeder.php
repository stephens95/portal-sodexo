<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProgramUpdatesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'version'     => 'v1.0.0',
                'title'       => 'Initial Release',
                'description' => 'The first release of the system with basic core features implemented.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert ke tabel
        $this->db->table('program_updates')->insertBatch($data);
    }
}
