<?php

namespace Modules\MaterialProc\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MaterialProc\Database\Seeders\Fake\ProcurementFakeSeeder;
use Modules\MaterialProc\Database\Seeders\Fake\SupplierFakeSeeder;

class MaterialProcDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // master data
        $this->call([
            SupplierFakeSeeder::class,
        ]);

        // transaction data
        $this->call([
            ProcurementFakeSeeder::class,
        ]);
    }
}
