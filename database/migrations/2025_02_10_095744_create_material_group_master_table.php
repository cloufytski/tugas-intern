<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_group_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_category')->references('id')->on('material_category_master')->onDelete('cascade');
            $table->string('product_group', length: 50)->index(); // because duplicate in WIP
            $table->double('min_threshold')->nullable(); // min_threshold is per year
            $table->double('max_threshold')->nullable(); // max_threshold is per year
            $table->timestamps();
            $table->softDeletes();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_group_master');
    }
};
