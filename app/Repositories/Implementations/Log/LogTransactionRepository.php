<?php

namespace App\Repositories\Implementations\Log;

use App\Models\Log\LogTransaction;

class LogTransactionRepository
{
    public function query()
    {
        return LogTransaction::query()
            ->orderByDesc('created_at');
    }
}
