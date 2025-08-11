<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addKey('role_id', true);
        $this->forge->createTable('roles');
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
