<?php

namespace Modules\InvBalance\Repositories\Implementations;

use App\Helpers\Utils;
use App\Models\Log\LogTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\InvBalance\Models\InventoryProcurement;
use Modules\InvBalance\Repositories\Interfaces\InventoryProcurementRepositoryInterface;

class InventoryProcurementRepository implements InventoryProcurementRepositoryInterface
{
    protected $modelName = 'INVENTORY_PROCUREMENT';
    protected $table = 'inventory_procurement_view';

    public function getInventoryProcurementGroup(string $dateGroup, string $startDate, string $endDate, ?array $categoryIds, ?array $groupIds)
    {
        $dateFormat = Utils::getSqlDateFormatByDateGroup($dateGroup);

        $selects = [
            DB::raw("TO_CHAR($this->table.eta, '{$dateFormat}') as period"),
            "$this->table.id_group",
            "$this->table.id_category",
            DB::raw("ROUND(SUM($this->table.total_actual)::NUMERIC, 3) as total_actual"),
            DB::raw("ROUND(SUM($this->table.total_plan)::NUMERIC, 3) as total_plan"),
            'material_group_master.product_group',
        ];

        $groups = [
            DB::raw("TO_CHAR($this->table.eta, '{$dateFormat}')"),
            "$this->table.id_group",
            "$this->table.id_category",
            'material_group_master.product_group',
        ];

        $eloquent = InventoryProcurement::join('material_group_master', "$this->table.id_group", '=', 'material_group_master.id')
            ->whereBetween("$this->table.eta", [$startDate, $endDate])
            ->select($selects);

        if ($categoryIds) {
            $eloquent->whereIn("$this->table.id_category", $categoryIds);
        }
        if ($groupIds) {
            $eloquent->whereIn("$this->table.id_group", $groupIds);
        }
        $eloquent
            ->groupBy($groups)
            ->orderByRaw("period ASC")
            ->orderBy("$this->table.id_group", 'ASC');

        return $eloquent->get();
    }

    public function refreshInventoryProcurementView()
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
            'log_module' => 'MaterialProc',
            'log_type' => $logType,
            'log_model' => $this->modelName,
            'log_description' => $logDescription,
        ]);
    }
}
