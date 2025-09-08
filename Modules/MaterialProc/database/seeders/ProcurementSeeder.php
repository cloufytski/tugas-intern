<?php

namespace Modules\MaterialProc\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MaterialProc\Imports\ProcurementImport;
use Modules\MaterialProc\Services\ProcurementService;

class ProcurementSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = module_path('MaterialProc', 'database/csv_import/RM_Receiving.xlsx');
        $sheetName = 'Procurement';

        if (file_exists($filePath)) {
            DB::table('procurement_ts')->truncate();
            Excel::import(new ProcurementImport($sheetName, app(ProcurementService::class)), $filePath);
            $this->command->info($sheetName . ' imported sucessfully!');

            // Increment id sequence
            DB::statement("SELECT setval('procurement_ts_id_seq', (SELECT MAX(id) FROM procurement_ts))");
        } else {
            $this->command->error("File not found at: " . $filePath);
        }
    }
}
