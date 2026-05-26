<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'           => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'expense_title'   => ['type' => 'VARCHAR', 'constraint' => 200],
            'category'        => ['type' => 'VARCHAR', 'constraint' => 80, 'default' => 'office'],
            'amount'          => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'expense_date'    => ['type' => 'DATE'],
            'payment_method'  => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'cash'],
            'reference_no'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'attachment_path' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_by_un_id'=> ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('company_un_id');
        $this->forge->addKey('category');
        $this->forge->addKey('expense_date');
        $this->forge->createTable('expenses');
    }

    public function down()
    {
        $this->forge->dropTable('expenses', true);
    }
}
