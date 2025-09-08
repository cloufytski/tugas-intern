<?php

namespace Database\Seeders\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialPackaging;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialPackagingSeeder extends Seeder
{
    private $table = 'material_packaging_master';
    public function run(): void
    {
        $data = [
            ['id' => 1, 'packaging' => 'BAG'],
            ['id' => 2, 'packaging' => 'BULK'],
            ['id' => 3, 'packaging' => 'DRUM'],
            ['id' => 4, 'packaging' => 'FLAKES'],
            ['id' => 5, 'packaging' => 'FLEXITANK'],
            ['id' => 6, 'packaging' => 'IBC'],
            ['id' => 7, 'packaging' => 'ISOTANK'],
            ['id' => 8, 'packaging' => 'MD'],
            ['id' => 9, 'packaging' => 'OMD'],
            ['id' => 10, 'packaging' => 'PASTILLES'],
        ];

        MaterialPackaging::truncate();
        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'MATERIAL_PACKAGING',
            'log_description' => "SEED MATERIAL_PACKAGING $count DATA",
        ]);
    }
}
