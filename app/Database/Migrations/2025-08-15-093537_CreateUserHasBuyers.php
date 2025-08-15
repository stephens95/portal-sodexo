<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserHasBuyers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'buyer_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        
        $this->forge->addKey(['user_id', 'buyer_id'], true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buyer_id', 'buyers', 'buyer_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_has_buyers');
    }

    public function down()
    {
        $this->forge->dropTable('user_has_buyers');
    }
}
