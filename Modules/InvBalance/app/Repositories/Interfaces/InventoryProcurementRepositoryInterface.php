<?php

namespace Modules\InvBalance\Repositories\Interfaces;

interface InventoryProcurementRepositoryInterface
{
    public function refreshInventoryProcurementView();

    public function getInventoryProcurementGroup(string $dateGroup, string $startDate, string $endDate, ?array $categoryIds, ?array $groupIds);
}
