<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * ERP integration pass:
 *  - sale_items.product_un_id  → link sales lines to product catalog
 *  - grv_items.vat             → returns match VAT-inclusive invoice lines
 *  - purchases / purchase_items → vendor purchase flow (payable increase + stock-in)
 */
class ErpIntegration extends Migration
{
    public function up()
    {
        // 1. Link sale items to products
        $this->forge->addColumn('sale_items', [
            'product_un_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'sale_un_id',
            ],
        ]);

        // 2. VAT on GRV items
        $this->forge->addColumn('grv_items', [
            'vat' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'after'      => 'unit_price',
            ],
        ]);

        // 3. Purchases (vendor bills)
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'un_id'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'purchase_no'      => ['type' => 'VARCHAR', 'constraint' => 40],
            'vendor_un_id'     => ['type' => 'VARCHAR', 'constraint' => 60],
            'company_un_id'    => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'purchase_date'    => ['type' => 'DATE'],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'discount'         => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'total_amount'     => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'paid_amount'      => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'due_amount'       => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'received'], // draft|received
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_by_un_id' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('vendor_un_id');
        $this->forge->createTable('purchases');

        // 4. Purchase line items
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'un_id'          => ['type' => 'VARCHAR', 'constraint' => 60],
            'purchase_un_id' => ['type' => 'VARCHAR', 'constraint' => 60],
            'product_un_id'  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'product_name'   => ['type' => 'VARCHAR', 'constraint' => 200],
            'unit'           => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pcs'],
            'quantity'       => ['type' => 'DECIMAL', 'constraint' => '12,3', 'default' => 0],
            'unit_cost'      => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'total'          => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('un_id');
        $this->forge->addKey('purchase_un_id');
        $this->forge->createTable('purchase_items');
    }

    public function down()
    {
        $this->forge->dropColumn('sale_items', 'product_un_id');
        $this->forge->dropColumn('grv_items', 'vat');
        $this->forge->dropTable('purchase_items', true);
        $this->forge->dropTable('purchases', true);
    }
}
