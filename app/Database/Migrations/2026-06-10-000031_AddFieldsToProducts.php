<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToProducts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'vendor_un_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'company_un_id',
            ],
            'cost_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'after'      => 'default_price',
            ],
        ]);

        $db = \Config\Database::connect();
        $db->query('CREATE INDEX idx_products_vendor ON products (vendor_un_id)');
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['vendor_un_id', 'cost_price']);
    }
}
