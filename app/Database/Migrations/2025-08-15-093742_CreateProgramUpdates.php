<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramUpdates extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'version'      => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'title'        => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'description'  => [
                'type' => 'TEXT',
            ],
            'created_at'   => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('program_updates');
    }

    public function down()
    {
        $this->forge->dropTable('program_updates');
    }
}
