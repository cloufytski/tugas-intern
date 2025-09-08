<?php

namespace Modules\InvBalance\Repositories\Interfaces;

interface InventorySalesRepositoryInterface
{
    public function getInventorySales(string $startDate, string $endDate, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds);
    public function refreshInventorySalesView();

    public function getInventorySalesGroup(string $dateGroup, string $startDate, string $endDate, ?array $orderStatusIds, ?array $categoryIds, ?array $groupIds);
}
