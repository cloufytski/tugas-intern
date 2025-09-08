<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait RefreshModuleDatabase
{
    protected function refreshModuleDatabase(string $moduleName): void
    {
        // Create migrations table manually
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        }

        Artisan::call('module:migrate-refresh', [
            'module' => $moduleName,
            '--seed' => false,
            '--database' => config('database.default'),
        ]);

        // ensure FK check are off every time DB is refreshed
        DB::connection()->getPdo()->exec('PRAGMA foreign_keys = OFF;');
    }
}
