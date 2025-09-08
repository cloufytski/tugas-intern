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
        Schema::create('procurement_history_ts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_procurement')->references('id')->on('procurement_ts')->onDelete('cascade');
            $table->string('field_name', length: 50); // 'qty_actual' or 'qty_plan'
            $table->string('old_value', length: 100)->nullable();
            $table->string('new_value', length: 100)->nullable();
            $table->text('history_remarks')->nullable();

            $table->timestamps();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_history_ts');
    }
};
