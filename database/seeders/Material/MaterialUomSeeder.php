<?php

namespace Database\Seeders\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialUom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialUomSeeder extends Seeder
{
    private $table = 'material_uom_master';
    public function run(): void
    {
        $data = [
            ['id' => 1, 'uom' => 'BAG'],
            ['id' => 2, 'uom' => 'DRUM'],
            ['id' => 3, 'uom' => 'IBC'],
            ['id' => 4, 'uom' => 'MT'],
        ];

        MaterialUom::truncate();
        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'MATERIAL_UOM',
            'log_description' => "SEED MATERIAL_UOM $count DATA",
        ]);
    }
}
