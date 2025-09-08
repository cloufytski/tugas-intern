<?php

namespace Modules\MaterialProc\Repositories\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ProcurementRepositoryInterface extends BaseRepositoryInterface
{
    public function query();
    public function getTotalByDateGroup(?string $startDate, ?string $endDate, array $params);
}
