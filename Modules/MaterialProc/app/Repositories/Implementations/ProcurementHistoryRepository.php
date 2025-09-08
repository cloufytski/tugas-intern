<?php

namespace Modules\MaterialProc\Repositories\Implementations;

use App\Traits\LogTransactionTrait;
use Modules\MaterialProc\Models\ProcurementHistory;

class ProcurementHistoryRepository
{
    use LogTransactionTrait;
    protected $logModule = 'MaterialProc';
    protected $modelName = 'PROCUREMENT_HISTORY';

    public function create(array $data)
    {
        $model = ProcurementHistory::create($data);
        $this->log(logType: 'CREATE', model: $model, data: $data);
        return $model;
    }
}
