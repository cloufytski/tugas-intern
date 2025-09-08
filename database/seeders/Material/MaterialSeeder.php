<?php

namespace Database\Seeders\Material;

use App\Imports\MaterialImport;
use App\Models\Log\LogTransaction;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('csv_import/Material_Class_Master.xlsx');
        $sheetName = 'Material';

        if (file_exists($filePath)) {
            $this->command->info('Start importing Material...');
            (new MaterialImport($sheetName))->withOutput($this->command->getOutput())->import($filePath);
            $this->command->info('Material imported sucessfully!');

            LogTransaction::insert([
                'log_module' => 'MasterData',
                'log_type' => 'SEED',
                'log_model' => 'MATERIAL',
                'log_description' => "SEED MATERIAL DATA",
            ]);
        } else {
            $this->command->error("File not found at: " . $filePath);
            LogTransaction::insert([
                'log_module' => 'MasterData',
                'log_type' => 'SEED FAILED',
                'log_model' => 'MATERIAL',
                'log_description' => "SEED FAILED MATERIAL DATA, FILE NOT FOUND",
            ]);
        }
    }
}
