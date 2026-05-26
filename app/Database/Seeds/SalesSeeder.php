<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SalesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('sales')->countAllResults() > 0) {
            return;
        }
        $companies  = $this->db->table('companies')->select('un_id')->get()->getResultArray();
        $customers  = $this->db->table('customers')->select('un_id')->get()->getResultArray();
        $containers = $this->db->table('containers')->select('un_id, product_name')->get()->getResultArray();
        if (empty($companies) || empty($customers)) {
            return;
        }
        $now = date('Y-m-d H:i:s');

        // Create 25 sales spread across the last 30 days
        for ($i = 0; $i < 25; $i++) {
            $customer  = $customers[array_rand($customers)];
            $company   = $companies[array_rand($companies)];
            $container = $containers ? $containers[array_rand($containers)] : null;
            $type      = random_int(0, 1) ? SALE_CASH : SALE_CREDIT;
            $subtotal  = random_int(8000, 180000);
            $discount  = (int) ($subtotal * (random_int(0, 5) / 100));
            $tax       = 0;
            $total     = $subtotal - $discount + $tax;
            $paid      = $type === SALE_CASH ? $total : (int) ($total * (random_int(20, 100) / 100));
            $due       = max(0, $total - $paid);
            $status    = $due === 0 ? PAYMENT_PAID : ($paid > 0 ? PAYMENT_PARTIAL : PAYMENT_DUE);

            $invoiceNo = 'INV-' . date('Y') . '-' . str_pad((string) ($i + 1001), 5, '0', STR_PAD_LEFT);
            $saleUnId  = generate_un_id('SAL');
            $saleDate  = date('Y-m-d', strtotime('-' . random_int(0, 30) . ' days'));

            $this->db->table('sales')->insert([
                'un_id'           => $saleUnId,
                'invoice_no'      => $invoiceNo,
                'company_un_id'   => $company['un_id'],
                'customer_un_id'  => $customer['un_id'],
                'container_un_id' => $container['un_id'] ?? null,
                'sale_type'       => $type,
                'sale_date'       => $saleDate,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'tax'             => $tax,
                'total_amount'    => $total,
                'paid_amount'     => $paid,
                'due_amount'      => $due,
                'payment_status'  => $status,
                'created_at'      => $saleDate . ' 10:00:00',
                'updated_at'      => $now,
            ]);

            // Add 1-3 line items per sale
            $itemCount = random_int(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $qty   = random_int(50, 800);
                $price = random_int(40, 220);
                $this->db->table('sale_items')->insert([
                    'un_id'        => generate_un_id('ITM'),
                    'sale_un_id'   => $saleUnId,
                    'product_name' => $container['product_name'] ?? 'Mixed Produce',
                    'quantity'     => $qty,
                    'unit'         => 'kg',
                    'unit_price'   => $price,
                    'total'        => $qty * $price,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }

            // If anything was paid, record a payment row
            if ($paid > 0) {
                $this->db->table('sale_payments')->insert([
                    'un_id'          => generate_un_id('SPM'),
                    'sale_un_id'     => $saleUnId,
                    'customer_un_id' => $customer['un_id'],
                    'amount'         => $paid,
                    'payment_method' => 'cash',
                    'payment_date'   => $saleDate,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        }
    }
}
