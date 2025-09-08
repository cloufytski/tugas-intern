<?php

namespace Database\Seeders\Material;

use App\Models\Log\LogTransaction;
use App\Models\Master\Material\MaterialMetric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialMetricSeeder extends Seeder
{
    private $table = 'material_metric_master';
    public function run(): void
    {
        // product_metric (47 rows)
        $data = [
            ['id_category' => 1, 'product_metric' => 'Acid LC Others'],
            ['id_category' => 1, 'product_metric' => 'Oleic Acid'],
            ['id_category' => 2, 'product_metric' => 'Acid SC Others'],
            ['id_category' => 2, 'product_metric' => 'AC80'],
            ['id_category' => 3, 'product_metric' => 'AEO'],
            ['id_category' => 4, 'product_metric' => 'FA16'],
            ['id_category' => 4, 'product_metric' => 'FA18'],
            ['id_category' => 4, 'product_metric' => 'FA68 Other Blend'],
            ['id_category' => 4, 'product_metric' => 'FA68/30'],
            ['id_category' => 5, 'product_metric' => 'FA12/FA14 Single Cut'],
            ['id_category' => 5, 'product_metric' => 'FA24'],
            ['id_category' => 5, 'product_metric' => 'FA24 Other Blends'],
            ['id_category' => 6, 'product_metric' => 'Alc SC Blends'],
            ['id_category' => 6, 'product_metric' => 'FA10'],
            ['id_category' => 6, 'product_metric' => 'FA8'],
            ['id_category' => 7, 'product_metric' => 'BD OTHERS'],
            ['id_category' => 7, 'product_metric' => 'ECOROL-WAX M'],
            ['id_category' => 7, 'product_metric' => 'ECOROL-WAX B'],
            ['id_category' => 7, 'product_metric' => 'ECOROL-WAX BF'],
            ['id_category' => 7, 'product_metric' => 'FFA'],
            ['id_category' => 8, 'product_metric' => 'Chemicals'],
            ['id_category' => 9, 'product_metric' => 'Ester Others'],
            ['id_category' => 9, 'product_metric' => 'GTCC'],
            ['id_category' => 9, 'product_metric' => 'OE'],
            ['id_category' => 9, 'product_metric' => 'TMPTO'],
            ['id_category' => 10, 'product_metric' => 'Fatty Amine'],
            ['id_category' => 11, 'product_metric' => 'Gly'],
            ['id_category' => 12, 'product_metric' => 'ME Longchain'],
            ['id_category' => 13, 'product_metric' => 'ME Shortchain'],
            ['id_category' => 14, 'product_metric' => 'CNO'],
            ['id_category' => 14, 'product_metric' => 'CPKO'],
            ['id_category' => 14, 'product_metric' => 'CPKOL'],
            ['id_category' => 14, 'product_metric' => 'CPO'],
            ['id_category' => 14, 'product_metric' => 'CPO-DMO'],
            ['id_category' => 14, 'product_metric' => 'EO'],
            ['id_category' => 14, 'product_metric' => 'RBDPKO'],
            ['id_category' => 14, 'product_metric' => 'RBDPKOL'],
            ['id_category' => 14, 'product_metric' => 'RBDPO'],
            ['id_category' => 14, 'product_metric' => 'RBDPOL-DMO'],
            ['id_category' => 14, 'product_metric' => 'RBDPS'],
            ['id_category' => 14, 'product_metric' => 'TMP'],
            ['id_category' => 15, 'product_metric' => 'Sorbitol'],
            ['id_category' => 16, 'product_metric' => 'UFA OTHERS'],
            ['id_category' => 16, 'product_metric' => 'UFA50/55'],
            ['id_category' => 16, 'product_metric' => 'UFA70/75'],
            ['id_category' => 17, 'product_metric' => 'WIP'],
        ];

        MaterialMetric::truncate();
        DB::table($this->table)->insert($data);

        // Increment id sequence
        DB::statement("SELECT setval('{$this->table}_id_seq', (SELECT MAX(id) FROM {$this->table}))");

        $count = count($data);
        LogTransaction::insert([
            'log_module' => 'MasterData',
            'log_type' => 'SEED',
            'log_model' => 'MATERIAL_METRIC',
            'log_description' => "SEED MATERIAL_METRIC $count DATA",
        ]);
    }
}
