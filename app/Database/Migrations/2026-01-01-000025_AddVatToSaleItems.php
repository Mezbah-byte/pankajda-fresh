<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVatToSaleItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sale_items', [
            'vat' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'null'       => false,
                'after'      => 'unit_price',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sale_items', 'vat');
    }
}
