<?php

namespace App\Repositories\Interfaces;

interface MaterialRepositoryInterface extends MasterRepositoryInterface
{
    public function findByMaterialDescription(string $materialDescription);
    public function firstByMaterialDescription(string $materialDescription);
}
