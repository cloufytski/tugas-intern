<?php

namespace Modules\InvBalance\Repositories\Implementations;

use App\Traits\LogTransactionTrait;
use Illuminate\Support\Facades\DB;
use Modules\InvBalance\Models\InventoryCheckpoint;
use Modules\InvBalance\Repositories\Interfaces\InventoryCheckpointRepositoryInterface;

class InventoryCheckpointRepository implements InventoryCheckpointRepositoryInterface
{
    use LogTransactionTrait;
    protected $modelName = 'INVENTORY_CHECKPOINT';
    protected $logModule = 'SalesPlan';

    public function findByDateRangeAndGroup(string $startDate, string $endDate, int $groupId)
    {
        return InventoryCheckpoint::whereBetween('date', [$startDate, $endDate])
            ->where('id_group', '=', $groupId)
            ->get();
    }

    public function findByDateRange(string $startDate, string $endDate)
    {
        return InventoryCheckpoint::whereBetween('date', [$startDate, $endDate])->get();
    }

    public function findByYearAndCategory(string $year, ?array $categoryIds)
    {
        return InventoryCheckpoint::whereHas('productGroup', function ($q) use ($categoryIds) {
            $q->whereIn('id_category', $categoryIds);
        })
            ->with('productGroup:id,id_category,product_group')
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();
    }

    public function updateOrCreate($data)
    {
        $model = InventoryCheckpoint::updateOrCreate(
            ['date' => $data['date'], 'id_group' => $data['id_group']],
            ['beginning_balance' => $data['beginning_balance'],]
        );
        if ($model->wasRecentlyCreated) {
            $this->log(logType: 'CREATE', model: $model, data: $data);
        } else {
            $this->log(logType: 'UPDATE', model: $model, data: $data);
        }
        return $model;
    }

    public function sumByYearAndProductGroup($year, array $groupIds = [])
    {
        $query = InventoryCheckpoint::whereYear('date', $year);
        if ($groupIds) {
            $query->whereIn('id_group', $groupIds);
        }
        return $query
            ->select(DB::raw('SUM(beginning_balance)'))
            ->get();
    }
}
