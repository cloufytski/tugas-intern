<?php

namespace Modules\InvBalance\Repositories\Implementations;

use App\Helpers\Utils;
use App\Models\Log\LogTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\InvBalance\Models\InventoryProduction;
use Modules\InvBalance\Repositories\Interfaces\InventoryProductionRepositoryInterface;

class InventoryProductionRepository implements InventoryProductionRepositoryInterface
{
    protected $modelName = 'INVENTORY_PRODUCTION';
    protected $table = 'inventory_production_view';

    // group by daily, weekly, monthly, yearly
    public function getInventoryProductionGroup(string $dateGroup, string $startDate, string $endDate, ?array $plantIds, ?array $categoryIds, ?array $groupIds)
    {
        $dateFormat = Utils::getSqlDateFormatByDateGroup($dateGroup);

        $selects = [
            DB::raw("TO_CHAR($this->table.week_start, '{$dateFormat}') as period"),
            "$this->table.id_plant",
            "$this->table.id_group",
            "$this->table.id_category",
            DB::raw("ROUND(SUM($this->table.total_projection)::NUMERIC, 3) as total_projection"),
            DB::raw("ROUND(SUM($this->table.total_actual)::NUMERIC, 3) as total_actual"),
            DB::raw("ROUND(SUM($this->table.total)::NUMERIC, 3) as total"),
            'material_group_master.product_group',
            'plant_master.description',
        ];

        $groups = [
            DB::raw("TO_CHAR($this->table.week_start, '{$dateFormat}')"),
            "$this->table.id_plant",
            "$this->table.id_group",
            "$this->table.id_category",
            'material_group_master.product_group',
            'plant_master.description',
        ];

        $eloquent = InventoryProduction::join('material_group_master', "$this->table.id_group", '=', 'material_group_master.id')
            ->join('plant_master', "$this->table.id_plant", '=', 'plant_master.id')
            ->whereBetween("$this->table.week_start", [$startDate, $endDate])
            ->select($selects);

        if ($plantIds) {
            $eloquent->whereIn("$this->table.id_plant", $plantIds);
        }
        if ($categoryIds) {
            $eloquent->whereIn("$this->table.id_category", $categoryIds);
        }
        if ($groupIds) {
            $eloquent->whereIn("$this->table.id_group", $groupIds);
        }
        $eloquent
            ->groupBy($groups)
            ->orderByRaw("period ASC")
            ->orderBy("$this->table.id_plant", 'ASC')
            ->orderBy("$this->table.id_group", 'ASC');

        return $eloquent->get();
    }

    // inventory daily only
    public function getInventoryProduction(string $startDate, string $endDate, ?array $plantIds, ?array $categoryIds, ?array $groupIds)
    {
        $eloquent = InventoryProduction::join('material_group_master', "$this->table.id_group", '=', 'material_group_master.id')
            ->join('plant_master', "$this->table.id_plant", '=', 'plant_master.id')
            ->select("$this->table.*", 'material_group_master.product_group', 'plant_master.description')
            ->whereBetween('week_start', [$startDate, $endDate]);

        if ($plantIds) {
            $eloquent->whereIn("$this->table.id_plant", $plantIds);
        }
        if ($categoryIds) {
            $eloquent->whereIn("$this->table.id_category", $categoryIds);
        }
        if ($groupIds) {
            $eloquent->whereIn("$this->table.id_group", $groupIds);
        }
        $eloquent->orderBy("$this->table.week_start", 'ASC')
            ->orderBy("$this->table.id_plant", 'ASC')
            ->orderBy("$this->table.id_group", 'ASC');

        return $eloquent->get();
    }

    public function refreshInventoryProductionView()
    {
        $statement = DB::statement("REFRESH MATERIALIZED VIEW {$this->table}");
        if ($statement) {
            $timestamp = Carbon::now();
            $this->customLog('REFRESH', "REFRESH $this->modelName ON $timestamp");
        }
        return $statement;
    }

    private function customLog(string $logType, string $logDescription)
    {
        LogTransaction::create([
            'log_module' => 'ProductionPlan',
            'log_type' => $logType,
            'log_model' => $this->modelName,
            'log_description' => $logDescription,
        ]);
    }
}
