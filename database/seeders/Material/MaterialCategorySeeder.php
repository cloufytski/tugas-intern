<?php

namespace Database\Seeders\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialCategorySeeder extends Seeder
{
    private $table = 'material_category_master';
    public function run(): void
    {
        $now = Carbon::now();
        // product_category (17 rows)
        $data = [
            ['product_category' => 'Acid Longchain', 'created_at' => $now],
            ['product_category' => 'Acid Shortchain', 'created_at' => $now],
            ['product_category' => 'AEO', 'created_at' => $now],
            ['product_category' => 'Alc Longchain', 'created_at' => $now],
            ['product_category' => 'Alc Midcut', 'created_at' => $now],
            ['product_category' => 'Alc Shortchain', 'created_at' => $now],
            ['product_category' => 'BD', 'created_at' => $now],
            ['product_category' => 'Chemicals', 'created_at' => $now],
            ['product_category' => 'Ester', 'created_at' => $now],
            ['product_category' => 'Fatty Amine', 'created_at' => $now],
            ['product_category' => 'Gly', 'created_at' => $now],
            ['product_category' => 'ME Longchain', 'created_at' => $now],
            ['product_category' => 'ME Shortchain', 'created_at' => $now],
            ['product_category' => 'RM', 'created_at' => $now],
            ['product_category' => 'Sorbitol', 'created_at' => $now],
            ['product_category' => 'UFA', 'created_at' => $now],
            ['product_category' => 'WIP', 'created_at' => $now],
        ];

        MaterialCategory::truncate();
        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'MATERIAL_CATEGORY',
            'log_description' => "SEED MATERIAL_CATEGORY $count DATA",
        ]);
    }
}
