<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_module', length: 20)->foreign('log_module')->references('module')->on('log_module');
            $table->foreignId('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('menu'); // submenu
            $table->string('filter_tag')->nullable(); // for multi-level filter (instead of creating parent table)
            $table->json('value'); // filter conditions json
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
