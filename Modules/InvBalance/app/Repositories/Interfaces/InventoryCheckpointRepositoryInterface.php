<?php

namespace Modules\InvBalance\Repositories\Interfaces;

interface InventoryCheckpointRepositoryInterface
{
    public function updateOrCreate($data);
    public function findByDateRange(string $startDate, string $endDate);
    public function findByDateRangeAndGroup(string $startDate, string $endDate, int $groupId);
    public function findByYearAndCategory(string $year, ?array $categoryIds);
    public function sumByYearAndProductGroup($year, array $groupIds = []);
}
