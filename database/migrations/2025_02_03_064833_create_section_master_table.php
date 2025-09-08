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
        // will be exposed as master data API
        Schema::create('section_master', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_plant')->references('id')->on('plant_master');
            $table->string('section', length: 20)->unique();
            $table->string('description', length: 50)->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('section_master');
    }
};
