<?php

namespace Database\Seeders;

use App\Models\Log\LogTransaction;
use App\Models\Master\Plant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            ['plant' => '1001', 'description' => 'EOMB', 'created_at' => $now],
            ['plant' => '1002', 'description' => 'EOB1', 'created_at' => $now],
            ['plant' => '1003', 'description' => 'EOB2', 'created_at' => $now],
            ['plant' => '1007', 'description' => 'EOB3', 'created_at' => $now],
            ['plant' => '1008', 'description' => 'EOJ', 'created_at' => $now],
            ['plant' => '1050', 'description' => 'UFA', 'created_at' => $now],
            ['plant' => '1060', 'description' => 'MPR', 'created_at' => $now],
        ];

        Plant::truncate();
        Plant::insert($data);

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'PLANT',
            'log_description' => "SEED PLANT $count DATA",
        ]);
    }
}
