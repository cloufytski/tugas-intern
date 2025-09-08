<?php

namespace Modules\MaterialProc\Repositories\Interfaces;

use App\Repositories\Interfaces\MasterRepositoryInterface;

interface SupplierRepositoryInterface extends MasterRepositoryInterface
{
    public function findBySupplier(string $supplier); // get collection by supplier name
    public function firstBySupplier(string $supplier); // get one for matching name
}
