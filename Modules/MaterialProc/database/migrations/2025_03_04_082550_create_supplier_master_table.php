<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_ms', function (Blueprint $table) {
            $table->id();
            $table->string('supplier');
             $table->string('certificate_no', 50)->nullable();

            $table->string('created_by', length: 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', length: 50)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_ms');
    }
};
