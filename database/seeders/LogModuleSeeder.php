<?php

namespace Database\Seeders;

use App\Models\Log\LogModule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LogModuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        LogModule::truncate();
        LogModule::insert([
            ['module' => 'SalesPlan', 'created_at' => $now],
            ['module' => 'ProductionPlan', 'created_at' => $now],
            ['module' => 'MaterialProc', 'created_at' => $now],
            ['module' => 'InvBalance', 'created_at' => $now],
        ]);
    }
}
