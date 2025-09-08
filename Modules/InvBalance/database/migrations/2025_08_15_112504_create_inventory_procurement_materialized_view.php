<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (App::environment('testing') || DB::getDriverName() === 'sqlite') {
            return;
        }
        if (!Schema::hasTable('procurement_ts')) {
            return;
        }
        DB::statement("
        CREATE MATERIALIZED VIEW inventory_procurement_view AS
        SELECT
            eta,
            CASE
                WHEN ffa <= 3 THEN 0
                WHEN ffa > 3 AND ffa <= 4 THEN 1
                WHEN ffa > 4 OR ffa IS NULL THEN 2
            END AS ffa_status,
            m.id_group as id_group,
            m.id_category as id_category,
            SUM(CASE WHEN p.qty_actual IS NOT NULL AND p.eta_actual IS NOT NULL THEN p.qty_actual ELSE 0 END) AS total_actual,
            SUM(CASE WHEN p.qty_actual IS NULL AND p.eta_actual IS NULL THEN p.qty_plan ELSE 0 END) AS total_plan
        FROM procurement_ts p
        JOIN material_master m on p.id_material = m.id
        GROUP BY eta, ffa_status, id_group, id_category
        ORDER BY eta, ffa_status, id_group, id_category ASC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (App::environment('testing') || DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS inventory_procurement_view");
    }
};
