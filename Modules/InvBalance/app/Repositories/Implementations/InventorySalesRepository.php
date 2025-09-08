<?php

namespace Modules\InvBalance\Repositories\Implementations;

use App\Helpers\Utils;
use App\Models\Log\LogTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\InvBalance\Models\InventorySales;
use Modules\InvBalance\Repositories\Interfaces\InventorySalesRepositoryInterface;

class InventorySalesRepository implements InventorySalesRepositoryInterface
{
    protected $modelName = 'INVENTORY_SALES';
    protected $table = 'inventory_sales_view';

    // group by daily, weekly, monthly, yearly
    public function getInventorySalesGroup(string $dateGroup, string $startDate, string $endDate, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds)
    {
        $dateFormat = Utils::getSqlDateFormatByDateGroup($dateGroup);

        $selects = [
            DB::raw("TO_CHAR($this->table.etd, '{$dateFormat}') as period"),
            "$this->table.id_order_status",
            "$this->table.id_group",
            "$this->table.id_category",
            DB::raw("ROUND(SUM($this->table.total)::NUMERIC, 3) AS total"),
            'material_group_master.product_group',
            'order_status_ms.order_status',
            'order_status_ms.badge_color',
        ];

        $groups = [
            DB::raw("TO_CHAR($this->table.etd, '{$dateFormat}')"),
            "$this->table.id_order_status",
            "$this->table.id_group",
            "$this->table.id_category",
            'material_group_master.product_group',
            'order_status_ms.order_status',
            'order_status_ms.badge_color',
        ];

        $eloquent = InventorySales::join('material_group_master', "$this->table.id_group", '=', 'material_group_master.id')
            ->join('order_status_ms', "$this->table.id_order_status", '=', 'order_status_ms.id')
            ->whereBetween('etd', [$startDate, $endDate])
            ->select($selects);

        if ($orderStatusIds) {
            $eloquent->whereIn("$this->table.id_order_status", $orderStatusIds);
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
            ->orderBy("$this->table.id_order_status", 'ASC')
            ->orderBy("$this->table.id_group", 'ASC');

        return $eloquent->get();
    }

    // inventory daily only
    public function getInventorySales(string $startDate, string $endDate, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds)
    {
        $eloquent = InventorySales::join('material_group_master', "$this->table.id_group", '=', 'material_group_master.id')
            ->join('order_status_ms', "$this->table.id_order_status", '=', 'order_status_ms.id')
            ->select("$this->table.*", 'material_group_master.product_group', 'order_status_ms.order_status', 'order_status_ms.badge_color')
            ->whereBetween('etd', [$startDate, $endDate]);

        if ($orderStatusIds) {
            $eloquent->whereIn("$this->table.id_order_status", $orderStatusIds);
        }
        if ($categoryIds) {
            $eloquent->whereIn("$this->table.id_category", $categoryIds);
        }
        if ($groupIds) {
            $eloquent->whereIn("$this->table.id_group", $groupIds);
        }
        $eloquent->orderBy("$this->table.etd", 'ASC')
            ->orderBy("$this->table.id_order_status", 'ASC')
            ->orderBy("$this->table.id_group", 'ASC');

        return $eloquent->get();
    }

    public function refreshInventorySalesView()
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
            'log_module' => 'SalesPlan',
            'log_type' => $logType,
            'log_model' => $this->modelName,
            'log_description' => $logDescription,
        ]);
    }
}
