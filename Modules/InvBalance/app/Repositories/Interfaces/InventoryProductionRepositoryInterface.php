<?php

namespace Modules\InvBalance\Repositories\Interfaces;

interface InventoryProductionRepositoryInterface
{
    public function getInventoryProduction(string $startDate, string $endDate, ?array $plantIds, ?array $categoryIds, ?array $groupIds);
    public function refreshInventoryProductionView();

    public function getInventoryProductionGroup(string $dateGroup, string $startDate, string $endDate, ?array $plantIds, ?array $categoryIds, ?array $groupIds);
}
