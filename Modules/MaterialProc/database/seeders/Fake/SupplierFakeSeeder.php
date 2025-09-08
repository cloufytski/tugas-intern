<?php

namespace Modules\MaterialProc\Database\Seeders\Fake;

use Illuminate\Database\Seeder;
use Modules\MaterialProc\Models\Supplier;

class SupplierFakeSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::truncate();
        Supplier::factory()->count(10)->create();
    }
}
