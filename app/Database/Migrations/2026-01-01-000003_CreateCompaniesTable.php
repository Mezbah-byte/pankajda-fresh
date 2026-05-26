<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_name'   => ['type' => 'VARCHAR', 'constraint' => 200],
            'company_type'   => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'trade_license'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tax_id'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'address'        => ['type' => 'TEXT', 'null' => true],
            'city'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'country'        => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'website'        => ['type' => 'VARCHAR', 'constraint' => 250, 'null' => true],
            'logo_path'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'currency'       => ['type' => 'VARCHAR', 'constraint' => 8, 'default' => 'BDT'],
            'opening_balance'=> ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_name');
        $this->forge->addKey('status');
        $this->forge->createTable('companies');
    }

    public function down()
    {
        $this->forge->dropTable('companies', true);
    }
}
