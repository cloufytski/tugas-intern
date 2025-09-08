<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_metric_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_category')->references('id')->on('material_category_master')->onDelete('cascade');
            $table->string('product_metric', length: 50)->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_metric_master');
    }
};
