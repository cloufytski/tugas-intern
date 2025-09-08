<?php

namespace App\Repositories\Interfaces;

interface MaterialUomRepositoryInterface
{
    public function searchByUom(string $uom);
    public function firstByUom(string $uom);
}
