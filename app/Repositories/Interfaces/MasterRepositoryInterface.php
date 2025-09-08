<?php

namespace App\Repositories\Interfaces;

interface MasterRepositoryInterface extends BaseRepositoryInterface
{
    public function query(); // NOTE: includes softDelete items, mainly used for DataTables with status
    public function restore(int $id);
}
