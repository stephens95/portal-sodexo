<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSOTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'invoice_date' => ['type' => 'DATE', 'null' => true],
            'invoice'      => ['type' => 'VARCHAR', 'constraint' => 30],
            'end_customer' => ['type' => 'VARCHAR', 'constraint' => 100],
            'inv'          => ['type' => 'DATETIME', 'null' => true],
            'pl'           => ['type' => 'DATETIME', 'null' => true],
            'bl'           => ['type' => 'DATETIME', 'null' => true],
            'coo'          => ['type' => 'DATETIME', 'null' => true],
            'ins'          => ['type' => 'DATETIME', 'null' => true],
            'note'         => ['type' => 'TEXT', 'null' => true],
            // 'po_ssa'     => ['type' => 'VARCHAR', 'constraint' => 30],
            // 'po_buyer'   => ['type' => 'VARCHAR', 'constraint' => 30],
            // 'end_cust'   => ['type' => 'VARCHAR', 'constraint' => 50],
            // 'so'         => ['type' => 'VARCHAR', 'constraint' => 20],
            // 'buyer_ssa'  => ['type' => 'VARCHAR', 'constraint' => 10],
            // 'style_ssa'  => ['type' => 'VARCHAR', 'constraint' => 10],
            // 'colour'     => ['type' => 'VARCHAR', 'constraint' => 10],
            // 'order_qty' => [
            //     'type' => 'INT',
            // ],
            // 'delv_note'     => ['type' => 'VARCHAR', 'constraint' => 15],
            // 'ship_qty'      => ['type' => 'VARCHAR', 'constraint' => 15],
            // 'outstd_po_qty' => ['type' => 'VARCHAR', 'constraint' => 15],
            // 'inv_numb'      => ['type' => 'VARCHAR', 'constraint' => 15],
            // 'created_at'    => ['type' => 'DATETIME', 'null' => true],
            // 'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('so_summary');
    }

    public function down()
    {
        $this->forge->dropTable('so_summary');
    }
}
