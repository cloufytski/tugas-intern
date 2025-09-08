<?php

namespace Database\Seeders;

use Database\Seeders\Material\MaterialCategorySeeder;
use Database\Seeders\Material\MaterialClassSeeder;
use Database\Seeders\Material\MaterialGroupSeeder;
use Database\Seeders\Material\MaterialGroupSimpleSeeder;
use Database\Seeders\Material\MaterialMetricSeeder;
use Database\Seeders\Material\MaterialPackagingClassSeeder;
use Database\Seeders\Material\MaterialPackagingSeeder;
use Database\Seeders\Material\MaterialSeeder;
use Database\Seeders\Material\MaterialUomSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // seed for Log
        $this->call([
            LogModuleSeeder::class,
            LogTransactionSeeder::class,
        ]);

        // seed for Laratrust user - permission - role in config/laratrust_seeder.php
        $this->call(LaratrustSeeder::class);

        // seed for Plant and Section Master
        $this->call([
            PlantSeeder::class,
            SectionSeeder::class,
        ]);

        // seed for Material
        $this->call([
            MaterialClassSeeder::class,
            MaterialCategorySeeder::class,
            MaterialGroupSeeder::class,
            MaterialMetricSeeder::class,
            MaterialGroupSimpleSeeder::class,
            MaterialUomSeeder::class,
            MaterialPackagingSeeder::class,
            MaterialPackagingClassSeeder::class,
            MaterialSeeder::class,
        ]);
    }
}
