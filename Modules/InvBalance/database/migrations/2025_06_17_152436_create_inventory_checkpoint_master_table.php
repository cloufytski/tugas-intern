<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // will be filled by admin
    public function up(): void
    {
        Schema::create('inventory_checkpoint_ms', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('id_group')->references('id')->on('material_group_master');
            $table->double('beginning_balance')->nullable();
            $table->timestamps();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_checkpoint_ms');
    }
};
