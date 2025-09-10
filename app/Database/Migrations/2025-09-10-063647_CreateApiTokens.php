<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiTokens extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'api_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'token'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'expired_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('api_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('api_tokens');
    }
}
