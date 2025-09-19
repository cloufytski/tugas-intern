<?php

namespace Modules\InvBalance\Services;

use App\Models\Log\LogTransaction;
use App\Repositories\Interfaces\MaterialGroupRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryCheckpointRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryProductionRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventorySalesRepositoryInterface;
use App\Helpers\Utils;
use Modules\InvBalance\Repositories\Interfaces\InventoryProcurementRepositoryInterface;

class InventoryBalanceService
{
    public function __construct(
        protected InventoryProductionRepositoryInterface $inventoryProductionRepository,
        protected InventorySalesRepositoryInterface $inventorySalesRepository,
        protected InventoryProcurementRepositoryInterface $inventoryProcurementRepository,
        protected InventoryCheckpointRepositoryInterface $inventoryCheckpointRepository,
        protected MaterialGroupRepositoryInterface $materialGroupRepository,
    ) {}

    public function refreshInventoryView()
    {
        $this->inventoryProductionRepository->refreshInventoryProductionView();
        $this->inventorySalesRepository->refreshInventorySalesView();
        $this->inventoryProcurementRepository->refreshInventoryProcurementView();
        return $this->getLogTimestamps();
    }

    public function getInventoryProduction(string $dateGroup, string $startDate, string $endDate, ?array $plantIds, ?array $categoryIds, ?array $groupIds)
    {
        return $this->inventoryProductionRepository->getInventoryProductionGroup(
            $dateGroup,
            $startDate,
            $endDate,
            $plantIds,
            $categoryIds,
            $groupIds,
        )->map(function ($item) use ($dateGroup, $startDate) {
            $item['date'] = Utils::constructPeriodToDate($item['period'], $dateGroup, $startDate);
            $item['total'] = floatval($item['total']);
            return $item;
        });
    }

    public function getInventorySales(string $dateGroup, string $startDate, string $endDate, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds)
    {
        return $this->inventorySalesRepository->getInventorySalesGroup(
            $dateGroup,
            $startDate,
            $endDate,
            $orderStatusIds,
            $categoryIds,
            $groupIds,
        )->map(function ($item) use ($dateGroup, $startDate) {
            $item['date'] = Utils::constructPeriodToDate($item['period'], $dateGroup, $startDate);
            $item['total'] = floatval($item['total']);
            return $item;
        });
    }

    public function getInventoryProcurement(string $dateGroup, string $startDate, string $endDate, ?array $categoryIds, ?array $groupIds)
    {
        return $this->inventoryProcurementRepository->getInventoryProcurementGroup(
            $dateGroup,
            $startDate,
            $endDate,
            $categoryIds,
            $groupIds,
        )->map(function ($item) use ($dateGroup, $startDate) {
            $item['date'] = Utils::constructPeriodToDate($item['period'], $dateGroup, $startDate);
            $item['total_actual'] = floatval($item['total_actual']);
            $item['total_plan'] = floatval($item['total_plan']);
            $item['total'] = floatval($item['total']);
            return $item;
        });
    }

    public function getInventoryBalance(string $dateGroup, string $startDate, string $endDate, ?array $plantIds, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds)
    {
        $dates = Utils::constructDates($startDate, $endDate)->toArray();
        if (empty($groupIds)) {
            $materialGroups = $this->materialGroupRepository->searchByCategories($categoryIds);
        } else {
            $materialGroups = $this->materialGroupRepository->searchByIds($groupIds);
        }
        $result = array_fill_keys($materialGroups->pluck('id')->all(), []);

        foreach ($materialGroups->pluck('id')->all() as $groupId) {
            // Initial date range, including empty date (only applicable to 'daily')
            if ($dateGroup === 'daily') {
                foreach ($dates as $date) {
                    $result[$groupId][$date] = [
                        'date' => $date,
                        'production' => 0,
                        'sales' => 0,
                    ];
                }
            }

            // Beginning
            $this->inventoryCheckpointRepository->findByDateRangeAndGroup($startDate, $endDate, $groupId)->each(function ($checkpoint) use (&$result, $dateGroup) {
                $weekStart = Utils::constructDateToPeriod($checkpoint->date, $dateGroup);
                $result[$checkpoint->id_group][$weekStart]['beginning'] = floatval($checkpoint->beginning_balance);
            });
        }

        // Prodution
        $this->getInventoryProduction(
            dateGroup: $dateGroup,
            startDate: $startDate,
            endDate: $endDate,
            plantIds: $plantIds,
            categoryIds: $categoryIds,
            groupIds: $groupIds
        )->groupBy('period')
            ->sortBy('id_plant')
            ->map(function ($group) use (&$result) {
                // $weekStart = $group->first()->week_start;
                $weekStart = $group->first()->period;
                foreach ($group as $item) {
                    $result[$item->id_group][$weekStart][$item->description] = floatval($item->total);
                    $result[$item->id_group][$weekStart]['production'] = ($result[$item->id_group][$weekStart]['production'] ?? 0) + floatval($item->total);
                }
            });

        // Sales
        $this->getInventorySales(
            dateGroup: $dateGroup,
            startDate: $startDate,
            endDate: $endDate,
            orderStatusIds: $orderStatusIds,
            categoryIds: $categoryIds,
            groupIds: $groupIds
        )->groupBy('period')
            ->sortBy('id_order_status')
            ->map(function ($group) use (&$result) {
                // $weekStart = $group->first()->etd;
                $weekStart = $group->first()->period;
                foreach ($group as $item) {
                    $result[$item->id_group][$weekStart][$item->order_status] = floatval($item->total);
                    $result[$item->id_group][$weekStart]['sales'] = ($result[$item->id_group][$weekStart]['sales'] ?? 0) + floatval($item->total);
                }
            });

        // Procurement - Receipt
        $this->getInventoryProcurement(
            dateGroup: $dateGroup,
            startDate: $startDate,
            endDate: $endDate,
            categoryIds: $categoryIds,
            groupIds: $groupIds
        )->groupBy('period')
            ->map(function ($group) use (&$result) {
                $weekStart = $group->first()->period;
                foreach ($group as $item) {
                    $result[$item->id_group][$weekStart]['receipt'] = ($result[$item->id_group][$weekStart]['receipt'] ?? 0) + floatval($item->total);
                }
            });

        // Transform structure
        $grouped = [];
        // Additional: total per Category; if there are Product Group selected, then only the total of those
        $grouped[$materialGroups->first()->category->product_category] = $this->getInventoryTotal($dateGroup, $startDate, $endDate, $plantIds, $orderStatusIds, $categoryIds, $groupIds)['data'];

        foreach ($result as $groupId => $dateEntries) {
            $materialGroup = $materialGroups->firstWhere('id', $groupId)->product_group;

            ksort($dateEntries); // sort by period
            foreach ($dateEntries as $date => $values) {
                $grouped[$materialGroup][] = array_merge($values, [
                    'date' => Utils::constructPeriodToDate($date, $dateGroup, $startDate),
                    'date_iso' => Utils::constructPeriodToDateISO($date, $dateGroup, $startDate),
                    'product_group' => $materialGroup,
                ]);
            }
        }
        return [
            'data' => $grouped,
            'log' => $this->getLogTimestamps(),
        ];
    }

    public function getInventoryTotal(string $dateGroup, string $startDate, string $endDate, ?array $plantIds, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds)
    {
        $result = [];
        $dates = Utils::constructDates($startDate, $endDate)->toArray();
        if (empty($groupIds)) {
            $materialGroups = $this->materialGroupRepository->searchByCategories($categoryIds);
        } else {
            $materialGroups = $this->materialGroupRepository->searchByIds($groupIds);
        }

        foreach ($materialGroups->pluck('id')->all() as $groupId) {
            // Initial date range, including empty date (only applicable to 'daily')
            if ($dateGroup === 'daily') {
                foreach ($dates as $date) {
                    $result[$date] = [
                        'date' => $date,
                        'production' => 0,
                        'sales' => 0,
                    ];
                }
            }

            // Beginning
            $this->inventoryCheckpointRepository->findByDateRangeAndGroup($startDate, $endDate, $groupId)->each(function ($checkpoint) use (&$result, $dateGroup) {
                $weekStart = Utils::constructDateToPeriod($checkpoint->date, $dateGroup);
                $result[$weekStart]['beginning'] = ($result[$weekStart]['beginning'] ?? 0) + floatval($checkpoint->beginning_balance);
            });
        }

        // Prodution
        $this->inventoryProductionRepository->getInventoryProductionGroup(
            dateGroup: $dateGroup,
            startDate: $startDate,
            endDate: $endDate,
            plantIds: $plantIds,
            categoryIds: $categoryIds,
            groupIds: $groupIds
        )->groupBy('period')
            ->sortBy('id_plant')
            ->map(function ($group) use (&$result) {
                $weekStart = $group->first()->period;
                foreach ($group as $item) {
                    $result[$weekStart][$item->description] = ($result[$weekStart][$item->description] ?? 0) + floatval($item->total);
                    $result[$weekStart]['production'] = ($result[$weekStart]['production'] ?? 0) + floatval($item->total);
                }
            });

        // Sales
        $this->inventorySalesRepository->getInventorySalesGroup(
            dateGroup: $dateGroup,
            startDate: $startDate,
            endDate: $endDate,
            orderStatusIds: $orderStatusIds,
            categoryIds: $categoryIds,
            groupIds: $groupIds
        )->groupBy('period')
            ->sortBy('id_order_status')
            ->map(function ($group) use (&$result) {
                $weekStart = $group->first()->period;
                foreach ($group as $item) {
                    $result[$weekStart][$item->order_status] = ($result[$weekStart][$item->order_status] ?? 0) + floatval($item->total);
                    $result[$weekStart]['sales'] = ($result[$weekStart]['sales'] ?? 0) + floatval($item->total);
                }
            });

        // Procurement - Receipt
        $this->inventoryProcurementRepository->getInventoryProcurementGroup(
            dateGroup: $dateGroup,
            startDate: $startDate,
            endDate: $endDate,
            categoryIds: $categoryIds,
            groupIds: $groupIds
        )->groupBy('period')
            ->map(function ($group) use (&$result) {
                $weekStart = $group->first()->period;
                foreach ($group as $item) {
                    $result[$weekStart]['receipt_actual'] =  ($result[$weekStart]['receipt_actual'] ?? 0) + floatval($item->total_actual);
                    $result[$weekStart]['receipt_plan'] =  ($result[$weekStart]['receipt_plan'] ?? 0) + floatval($item->total_plan);
                    $result[$weekStart]['receipt'] = ($result[$weekStart]['receipt'] ?? 0) + floatval($item->total_actual) + floatval($item->total_plan);
                }
            });

        $grouped = [];
        foreach ($result as $date => $values) {
            $grouped[] = array_merge($values, [
                'date' => Utils::constructPeriodToDate($date, $dateGroup, $startDate),
                'date_iso' => Utils::constructPeriodToDateISO($date, $dateGroup, $startDate),
            ]);
        }

        return [
            'data' => $grouped,
            'log' => $this->getLogTimestamps(),
        ];
    }

    public function getLogTimestamps()
    {
        $productionLog = LogTransaction::where([
            ['log_type', '=', 'REFRESH'],
            ['log_model', '=', 'INVENTORY_PRODUCTION']
        ])
            ->orderByDesc('created_at')
            ->first();
        $salesLog = LogTransaction::where([
            ['log_type', '=', 'REFRESH'],
            ['log_model', '=', 'INVENTORY_SALES']
        ])
            ->orderByDesc('created_at')
            ->first();
        $procurementLog = LogTransaction::where([
            ['log_type', '=', 'REFRESH'],
            ['log_model', '=', 'INVENTORY_PROCUREMENT']
        ])
            ->orderByDesc('created_at')
            ->first();
        return [
            'production_log' => $productionLog?->created_at,
            'sales_log' => $salesLog?->created_at,
            'procurement_log' => $procurementLog?->created_at,
        ];
    }
}
