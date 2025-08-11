<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserHasRoles extends Migration
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
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'role_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_has_roles');
    }

    public function down()
    {
        $this->forge->dropTable('user_has_roles');
    }
}
