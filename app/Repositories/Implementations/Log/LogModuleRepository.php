<?php

namespace App\Repositories\Implementations\Log;

use App\Models\Log\LogModule;

class LogModuleRepository
{
    public function query()
    {
        return LogModule::query()
            ->orderByDesc('created_at');
    }
}
