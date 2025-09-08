<?php

namespace Modules\MaterialProc\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MaterialProc\Models\Procurement;

class ProcurementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Procurement::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $hasActual = fake()->boolean(50); // ~50% with actuals, rest plan-only

        $qtyPlan = fake()->randomFloat(2, 1000, 10000);
        $etaPlan = fake()->dateTimeThisMonth();

        $qtyActual = $hasActual ? fake()->randomFloat(2, 1000, 10000) : null;
        $etaActual = $hasActual ? fake()->dateTimeThisMonth() : null;

        $qty = $hasActual ? $qtyActual : $qtyPlan;
        $eta = $hasActual ? $etaActual : $etaPlan;

        return [
            'contract_no' => fake()->randomNumber(5, true),
            'po_date' => fake()->dateTimeThisMonth(),
            'id_supplier' => fake()->randomDigitNot(0),
            'id_material' => fake()->numberBetween(91, 96),
            'id_plant' => fake()->numberBetween(2, 4),
            'qty' => $qty,
            'qty_actual' => $qtyActual,
            'qty_plan' => $qtyPlan,
            'eta' => $eta,
            'eta_actual' => $etaActual,
            'eta_plan' => $etaPlan,
            'vessel_name' => fake()->streetName(),
            'loading_port' => fake()->country(),
            'ffa' => fake()->randomFloat(2, 0, 1),
            'price' => fake()->randomFloat(2, 100, 1000),
            'is_rspo' => fake()->boolean(),
        ];
    }
}
