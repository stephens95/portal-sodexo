<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBuyers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'buyer_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'buyer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'country_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'group_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Sodexo Global',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('buyer_id', true);
        $this->forge->createTable('buyers');
    }

    public function down()
    {
        $this->forge->dropTable('buyers');
    }
}
