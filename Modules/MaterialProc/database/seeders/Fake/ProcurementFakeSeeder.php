<?php

namespace Modules\MaterialProc\Database\Seeders\Fake;

use Illuminate\Database\Seeder;
use Modules\MaterialProc\Models\Procurement;

class ProcurementFakeSeeder extends Seeder
{
    public function run(): void
    {
        Procurement::truncate();
        Procurement::factory()->count(50)->create();
    }
}
