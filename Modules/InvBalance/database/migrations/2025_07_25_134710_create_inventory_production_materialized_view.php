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
        if (!Schema::hasTable('prodsum_projection_ts') && !Schema::hasTable('prodsum_actual_ts')) {
            return;
        }
        $sql = "
        WITH proj AS (
            SELECT week_start, id_plant, m.id_group, m.id_category, ROUND(SUM(value)::NUMERIC, 3) AS total_projection
            FROM prodsum_projection_ts p
            JOIN material_master m ON p.id_material = m.id
            GROUP BY week_start, id_plant, m.id_group, m.id_category
                ),
        act AS (
            SELECT week_start, id_plant, m.id_group, m.id_category, ROUND(SUM(value)::NUMERIC, 3) AS total_actual
            FROM prodsum_actual_ts a
            JOIN material_master m ON a.id_material = m.id
            GROUP BY week_start, id_plant, m.id_group, m.id_category
        )
        ";
        DB::statement("
        CREATE MATERIALIZED VIEW inventory_production_view AS
        {$sql}
        SELECT
            COALESCE(p.week_start, a.week_start) AS week_start,
            COALESCE(p.id_plant, a.id_plant) AS id_plant,
            COALESCE(p.id_group, a.id_group) AS id_group,
            COALESCE(p.id_category, a.id_category) AS id_category,
            p.total_projection,
            a.total_actual,
            COALESCE(a.total_actual, p.total_projection) AS total
        FROM proj p
        FULL OUTER JOIN act a
            ON p.week_start = a.week_start
            AND p.id_plant = a.id_plant
            AND p.id_group = a.id_group
            AND p.id_category = a.id_category
        ORDER BY week_start ASC;
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
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS inventory_production_view");
    }
};
