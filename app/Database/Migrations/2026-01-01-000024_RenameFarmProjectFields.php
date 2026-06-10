<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameFarmProjectFields extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE farm_projects CHANGE crop_name item_name VARCHAR(150) NULL");
        $this->db->query("ALTER TABLE farm_projects CHANGE land_size quantity DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
        $this->db->query("ALTER TABLE farm_projects CHANGE land_unit quantity_unit VARCHAR(20) NOT NULL DEFAULT 'pcs'");
        $this->db->query("ALTER TABLE farm_projects CHANGE total_cost total_rate DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
        $this->db->query("ALTER TABLE farm_activities CHANGE cost rate DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE farm_projects CHANGE item_name crop_name VARCHAR(150) NULL");
        $this->db->query("ALTER TABLE farm_projects CHANGE quantity land_size DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
        $this->db->query("ALTER TABLE farm_projects CHANGE quantity_unit land_unit VARCHAR(20) NOT NULL DEFAULT 'acre'");
        $this->db->query("ALTER TABLE farm_projects CHANGE total_rate total_cost DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
        $this->db->query("ALTER TABLE farm_activities CHANGE rate cost DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
    }
}
