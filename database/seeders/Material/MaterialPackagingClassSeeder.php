<?php

namespace Database\Seeders\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialPackagingClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialPackagingClassSeeder extends Seeder
{
    private $table = 'material_packaging_class_master';
    public function run(): void
    {
        $data = [
            ['id' => 1, 'packaging_class' => 'BD'],
            ['id' => 2, 'packaging_class' => 'CHE'],
            ['id' => 3, 'packaging_class' => 'FG'],
            ['id' => 4, 'packaging_class' => 'FGP'],
            ['id' => 5, 'packaging_class' => 'RM'],
            ['id' => 6, 'packaging_class' => 'WIP'],
        ];
        MaterialPackagingClass::truncate();
        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'MATERIAL_PACKAGING_CLASS',
            'log_description' => "SEED MATERIAL_PACKAGING_CLASS $count DATA",
        ]);
    }
}
