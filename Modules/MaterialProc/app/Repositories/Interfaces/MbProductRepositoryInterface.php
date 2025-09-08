<?php

namespace Modules\MaterialProc\Repositories\Interfaces;

interface MbProductRepositoryInterface
{
    public function getInputProducts(string $startDate, string $endDate);
}
