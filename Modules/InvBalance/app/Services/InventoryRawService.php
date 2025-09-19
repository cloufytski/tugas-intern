<?php

namespace Modules\InvBalance\Services;

use App\Models\Log\LogTransaction;
use App\Repositories\Interfaces\MaterialGroupRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryCheckpointRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryProductionRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryProcurementRepositoryInterface;
use App\Helpers\Utils;

class InventoryRawService
{
    public function __construct(
        protected InventoryProductionRepositoryInterface $inventoryProductionRepository,
        protected InventoryProcurementRepositoryInterface $inventoryProcurementRepository,
        protected InventoryCheckpointRepositoryInterface $inventoryCheckpointRepository,
        protected MaterialGroupRepositoryInterface $materialGroupRepository,
    ) {}


    public function getInventoryProduction(
        string $dateGroup,
        string $startDate,
        string $endDate,
        ?array $plantIds,
        ?array $categoryIds,
        ?array $groupIds
    ) {
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

    public function getInventoryProcurement(
        string $dateGroup,
        string $startDate,
        string $endDate,
        ?array $categoryIds,
        ?array $groupIds
    ) {
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
            $item['vessel_port'] = $item['vessel_port'] ?? '';
            return $item;
        });
    }

    public function getInventoryRaw(
        string $dateGroup,
        string $startDate,
        string $endDate,
        ?array $plantIds,
        ?array $categoryIds,
        ?array $groupIds
    ) {
        $dates = Utils::constructDates($startDate, $endDate)->toArray();
        if (empty($groupIds)) {
            $materialGroups = $this->materialGroupRepository->searchByCategories($categoryIds ?? []);
        } else {
            $materialGroups = $this->materialGroupRepository->searchByIds($groupIds ?? []);
        }


        $result = array_fill_keys($materialGroups->pluck('id')->all(), []);

        foreach ($materialGroups->pluck('id')->all() as $groupId) {
            // Initial date range, including empty date (only applicable to 'daily')
            if ($dateGroup === 'daily') {
                foreach ($dates as $date) {
                    $result[$groupId][$date] = [
                        'date' => $date,
                        'beginning' => 0,
                        'receipt' => 0,
                        'production' => 0,
                    ];
                }
            }

            // Beginning Raw Material Balance
            $this->inventoryCheckpointRepository->findByDateRangeAndGroup($startDate, $endDate, $groupId)->each(function ($checkpoint) use (&$result, $dateGroup) {
                $period = Utils::constructDateToPeriod($checkpoint->date, $dateGroup);
                $result[$checkpoint->id_group][$period]['beginning'] = floatval($checkpoint->beginning_raw ?? $checkpoint->beginning_balance ?? 0);
            });
        }

        // Production (Raw Material Usage)
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
                $period = $group->first()->period;
                foreach ($group as $item) {
                    $result[$item->id_group][$period][$item->description] = floatval($item->total);
                    $result[$item->id_group][$period]['production'] = ($result[$item->id_group][$period]['production'] ?? 0) + floatval($item->total);
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
                $period = $group->first()->period;
                foreach ($group as $item) {
                    $result[$item->id_group][$period]['receipt'] = ($result[$item->id_group][$period]['receipt'] ?? 0) + floatval($item->total);
                    $result[$item->id_group][$period]['vessel_port'] = $item->vessel_port ?? '';
                }
            });

        // Transform structure
        $grouped = [];

        // Add category total first (if there are specific product groups selected, show only those totals)
        if ($materialGroups->isNotEmpty()) {
            $categoryName = $materialGroups->first()->category->product_category;
            $grouped[$categoryName] = $this->getInventoryTotal($dateGroup, $startDate, $endDate, $plantIds, $categoryIds, $groupIds)['data'];
        }

        // Add individual product group data
        foreach ($result as $groupId => $dateEntries) {
            $materialGroup = $materialGroups->firstWhere('id', $groupId);
            if (!$materialGroup) continue;

            $productGroupName = $materialGroup->product_group;

            ksort($dateEntries); // sort by period
            foreach ($dateEntries as $date => $values) {
                $beginning = $values['beginning'] ?? 0;
                $receipt = $values['receipt'] ?? 0;
                $production = $values['production'] ?? 0;

                $end = $beginning + $receipt - $production;
                $noOfDays = $production > 0 ? round($end / $production, 2) : 0;

                $grouped[$productGroupName][] = array_merge($values, [
                    'date' => Utils::constructPeriodToDate($date, $dateGroup, $startDate),
                    'date_iso' => Utils::constructPeriodToDateISO($date, $dateGroup, $startDate),
                    'product_group' => $productGroupName,
                    'end' => $end,
                    'no_of_days' => $noOfDays,
                ]);
            }
        }

        return [
            'data' => $grouped,
            'log' => $this->getLogTimestamps(),
        ];
    }

    public function getInventoryTotal(
        string $dateGroup,
        string $startDate,
        string $endDate,
        ?array $plantIds,
        ?array $categoryIds,
        ?array $groupIds
    ) {
        $result = [];
        $dates = Utils::constructDates($startDate, $endDate)->toArray();

        if (empty($groupIds)) {
            $materialGroups = $this->materialGroupRepository->searchByCategories($categoryIds ?? []);
        } else {
            $materialGroups = $this->materialGroupRepository->searchByIds($groupIds ?? []);
        }

        foreach ($materialGroups->pluck('id')->all() as $groupId) {
            if ($dateGroup === 'daily') {
                foreach ($dates as $date) {
                    $result[$date] = [
                        'date' => $date,
                        'beginning' => 0,
                        'receipt' => 0,
                        'production' => 0,
                    ];
                }
            }

            $this->inventoryCheckpointRepository
                ->findByDateRangeAndGroup($startDate, $endDate, $groupId)
                ->each(function ($checkpoint) use (&$result, $dateGroup) {
                    $period = Utils::constructDateToPeriod($checkpoint->date, $dateGroup);
                    $result[$period]['beginning'] = ($result[$period]['beginning'] ?? 0)
                        + floatval($checkpoint->beginning_raw ?? $checkpoint->beginning_balance ?? 0);
                });
        }

        // PRODUCTION
        $this->inventoryProductionRepository->getInventoryProductionGroup(
            $dateGroup,
            $startDate,
            $endDate,
            $plantIds,
            $categoryIds,
            $groupIds
        )
            ->groupBy('period')
            ->map(function ($periodGroup) use (&$result) {
                $period = $periodGroup->first()->period;

                foreach ($periodGroup as $row) {
                    $plantName = $row->description;
                    $qty = floatval($row->total);

                    $result[$period][$plantName] = ($result[$period][$plantName] ?? 0) + $qty;
                    $result[$period]['production'] = ($result[$period]['production'] ?? 0) + $qty;
                }
            });

        // RECEIPT
        $this->inventoryProcurementRepository->getInventoryProcurementGroup(
            $dateGroup,
            $startDate,
            $endDate,
            $categoryIds,
            $groupIds
        )
            ->groupBy('period')
            ->map(function ($periodGroup) use (&$result) {
                $period = $periodGroup->first()->period;

                foreach ($periodGroup as $row) {
                    $result[$period]['receipt'] = ($result[$period]['receipt'] ?? 0)
                        + floatval($row->total_actual)
                        + floatval($row->total_plan);
                    $result[$period]['vessel_port'] = $row->vessel_port ?? '';
                }
            });

        // Hitung end & no_of_days
        $grouped = [];
        foreach ($result as $period => $values) {
            $beginning = $values['beginning'] ?? 0;
            $receipt = $values['receipt'] ?? 0;
            $production = $values['production'] ?? 0;

            $end = $beginning + $receipt - $production;
            $noOfDays = $production > 0 ? round($end / $production, 2) : 0;

            $grouped[] = array_merge($values, [
                'date' => Utils::constructPeriodToDate($period, $dateGroup, $startDate),
                'date_iso' => Utils::constructPeriodToDateISO($period, $dateGroup, $startDate),
                'end' => $end,
                'no_of_days' => $noOfDays,
            ]);
        }

        return [
            'data' => $grouped,
            'log' => $this->inventoryBalanceService->getLogTimestamps(),
        ];
    }
}
