<?php

namespace Database\Seeders\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialClass;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialClassSeeder extends Seeder
{
    private $table = 'material_class_master';

    public function run(): void
    {
        $now = Carbon::now();
        // class (6 rows)
        $data = [
            ['class' => 'CHE', 'created_at' => $now],
            ['class' => 'FG', 'created_at' => $now],
            ['class' => 'FGB', 'created_at' => $now],
            ['class' => 'FGP', 'created_at' => $now],
            ['class' => 'RM', 'created_at' => $now],
            ['class' => 'WIP', 'created_at' => $now],
        ];

        MaterialClass::truncate();
        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");


        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'MATERIAL_CLASS',
            'log_description' => "SEED MATERIAL_CLASS $count DATA",
        ]);
    }
}
