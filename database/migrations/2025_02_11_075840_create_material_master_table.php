<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PPA/Databases/Material List.xlsx > Sheet1
        Schema::create('material_master', function (Blueprint $table) {
            $table->id();
            $table->string('material', length: 50)->nullable();
            $table->string('material_description')->index();
            $table->foreignId('id_class')->references('id')->on('material_class_master');
            $table->foreignId('id_category')->references('id')->on('material_category_master')->onDelete('cascade');
            $table->foreignId('id_metric')->references('id')->on('material_metric_master')->onDelete('cascade');
            $table->foreignId('id_group_simple')->references('id')->on('material_group_simple_master')->onDelete('cascade');
            $table->foreignId('id_group')->references('id')->on('material_group_master')->onDelete('cascade');
            $table->foreignId('id_packaging')->references('id')->on('material_packaging_master')->onDelete('cascade');
            $table->foreignId('id_uom')->references('id')->on('material_uom_master')->onDelete('cascade');
            $table->double('rate');
            $table->double('conversion');
            $table->double('space')->nullable();
            $table->foreignId('id_pp_class')->references('id')->on('material_packaging_class_master')->onDelete('cascade');
            $table->foreignId('id_pv_class')->references('id')->on('material_packaging_class_master')->onDelete('cascade');
            $table->string('kind_of_pack')->nullable();
            $table->double('base_price')->nullable();
            $table->string('pack_cost', length: 50)->nullable();
            $table->double('devider')->nullable();
            $table->string('bus_line')->nullable();
            $table->string('rm')->nullable();
            $table->string('conversion_to_rm')->nullable();
            $table->string('base_product')->nullable(); // refer to the ID of material
            $table->string('rumus_molecul')->nullable();
            $table->string('auto_produce', length: 20)->nullable();
            $table->boolean('eudr')->nullable();
            $table->boolean('eudr_sale')->nullable();
            $table->string('hs_code', length: 50)->nullable();

            $table->string('created_by', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_master');
    }
};
