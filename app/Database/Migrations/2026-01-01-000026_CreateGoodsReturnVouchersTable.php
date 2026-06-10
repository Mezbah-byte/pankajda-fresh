<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoodsReturnVouchersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'un_id'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'grv_no'           => ['type' => 'VARCHAR', 'constraint' => 50],
            'customer_un_id'   => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'company_un_id'    => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'sale_un_id'       => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'grv_date'         => ['type' => 'DATE'],
            'description'      => ['type' => 'TEXT', 'null' => true],
            'total_amount'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_by_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('un_id');
        $this->forge->addUniqueKey('grv_no');
        $this->forge->addKey('customer_un_id');
        $this->forge->addKey('grv_date');
        $this->forge->createTable('goods_return_vouchers');
    }

    public function down()
    {
        $this->forge->dropTable('goods_return_vouchers', true);
    }
}
