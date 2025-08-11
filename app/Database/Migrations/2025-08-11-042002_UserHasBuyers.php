<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserHasBuyers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
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
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('buyer_id', 'buyers', 'buyer_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_has_buyers');
    }

    public function down()
    {
        $this->forge->dropTable('user_has_buyers');
    }
}
