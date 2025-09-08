<?php

namespace Modules\MaterialProc\Services;

use Modules\MaterialProc\Repositories\Interfaces\MbProductRepositoryInterface;

class MbProductService
{
    public function __construct(
        protected MbProductRepositoryInterface $mbProductRepository,
    ) {}

    public function getInputProducts(string $startDate, string $endDate, bool $isRspo = true)
    {
        return $this->mbProductRepository->getInputProducts($startDate, $endDate, $isRspo);
    }
}
