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
                // 'unsigned' => true,
            ],
            'buyer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'group_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
