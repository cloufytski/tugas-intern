<?php

namespace Database\Seeders;

use App\Models\Log\LogTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LogTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        LogTransaction::truncate();
        LogTransaction::insert([
            ['log_module' => 'SalesPlan', 'log_type' => 'MODULE', 'log_description' => 'CREATE MODULE SalesPlan', 'created_at' => $now],
            ['log_module' => 'ProductionPlan', 'log_type' => 'MODULE', 'log_description' => 'CREATE MODULE ProductionPlan', 'created_at' => $now],
            ['log_module' => 'MaterialProc', 'log_type' => 'MODULE', 'log_description' => 'CREATE MODULE MaterialProc', 'created_at' => $now],
        ]);
    }
}
