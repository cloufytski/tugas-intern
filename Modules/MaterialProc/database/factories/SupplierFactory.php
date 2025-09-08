<?php

namespace Modules\MaterialProc\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MaterialProc\Models\Supplier;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'supplier' => fake()->company(),
            'certificate_no' => fake()->unique()->numerify('CERT-#####'),
        ];
    }
}
