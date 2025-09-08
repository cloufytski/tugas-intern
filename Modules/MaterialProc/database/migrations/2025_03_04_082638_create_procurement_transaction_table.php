<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_ts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_supplier')->references('id')->on('supplier_ms')->onDelete('cascade');
            $table->foreignId('id_material')->references('id')->on('material_master')->onDelete('cascade');
            $table->foreignId('id_plant')->references('id')->on('plant_master')->onDelete('cascade');
            $table->string('contract_no', length: 20)->nullable();
            $table->date('po_date')->nullable();
            $table->double('qty'); // business logic
            $table->double('qty_actual')->nullable();
            $table->double('qty_plan');
            $table->date('eta'); // business logic
            $table->date('eta_actual')->nullable();
            $table->date('eta_plan');
            $table->string('vessel_name')->nullable();
            $table->string('loading_port')->nullable();
            $table->double('ffa')->nullable();
            $table->double('price')->nullable();
            $table->boolean('is_rspo')->nullable();
            $table->text('remarks')->nullable();

            $table->string('created_by', length: 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', length: 50)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });

        // NOTE: Do not link to DPS Actual
    }

    public function down(): void
    {
        if (Schema::hasColumn('procurement_ts', 'id_prodsum_actual')) {
            Schema::table('procurement_ts', function (Blueprint $table) {
                $table->dropForeign(['id_prodsum_actual']);
                $table->dropColumn('id_prodsum_actual');
            });
        }
        Schema::dropIfExists('procurement_ts');
    }
};
