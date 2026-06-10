<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceCompaniesTable extends Migration
{
    public function up()
    {
        $fields = [
            'contact_person' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'phone'],
            'fax'            => ['type' => 'VARCHAR', 'constraint' => 30,  'null' => true, 'after' => 'contact_person'],
            'bank_name'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'after' => 'website'],
            'bank_account'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'bank_name'],
            'bank_routing'   => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'after' => 'bank_account'],
            'established_date' => ['type' => 'DATE', 'null' => true, 'after' => 'bank_routing'],
        ];
        $this->forge->addColumn('companies', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('companies', ['contact_person', 'fax', 'bank_name', 'bank_account', 'bank_routing', 'established_date']);
    }
}
