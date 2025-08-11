<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Jalankan semua seeder di sini
        $this->call(UsersSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(BuyersSeeder::class);
        $this->call(UserHasRolesSeeder::class);
        $this->call(UserHasBuyersSeeder::class);
        $this->call(ProgramUpdatesSeeder::class);
    }
}
