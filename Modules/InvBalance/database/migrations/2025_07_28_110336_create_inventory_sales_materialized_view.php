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
        if (!Schema::hasTable('order_projection_ts')) {
            return;
        }
        DB::statement("
        CREATE MATERIALIZED VIEW inventory_sales_view AS
        SELECT
            etd,
            id_order_status,
            m.id_group as id_group,
            m.id_category as id_category,
            SUM(qty) AS total
        FROM order_projection_ts o
        JOIN material_master m on o.id_material = m.id
        WHERE qty != 0
        GROUP BY etd, id_order_status, id_group, id_category
        ORDER BY etd ASC;
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
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS inventory_sales_view");
    }
};
