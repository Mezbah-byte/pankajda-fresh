<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceVisasTable extends Migration
{
    public function up()
    {
        // Add new columns to visas
        $this->forge->addColumn('visas', [
            'from_country' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'country',
            ],
            'work_permit_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'from_country',
            ],
            'work_permit_issue_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'work_permit_number',
            ],
            'work_permit_expiry_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'work_permit_issue_date',
            ],
            'purchase_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => false,
                'after'      => 'visa_expiry_date',
            ],
            'selling_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => false,
                'after'      => 'purchase_price',
            ],
            'extra_costs' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => false,
                'after'      => 'selling_price',
            ],
            'profit' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => false,
                'after'      => 'extra_costs',
            ],
        ]);

        // visa_extra_costs table
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'visa_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60],
            'description' => ['type' => 'VARCHAR', 'constraint' => 200],
            'amount'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('visa_un_id');
        $this->forge->createTable('visa_extra_costs');
    }

    public function down()
    {
        $this->forge->dropTable('visa_extra_costs', true);
        $this->forge->dropColumn('visas', ['from_country', 'work_permit_number', 'work_permit_issue_date',
            'work_permit_expiry_date', 'purchase_price', 'selling_price', 'extra_costs', 'profit']);
    }
}
