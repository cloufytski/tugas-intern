<?php

namespace Modules\MaterialProc\Helpers;

use Carbon\Carbon;

class MaterialUtils
{
    public static function constructPeriodToDate($period, $dateGroup)
    {
        switch ($dateGroup) {
            case 'yearly':
                return $period;
            case 'monthly':
                return Carbon::createFromFormat('Y-m', $period)->format('M-y');
            case 'weekly':
                [$year, $week] = explode('-', $period);
                return Carbon::now()->setISODate($year, $week)->startOfWeek(Carbon::MONDAY)->format('d-M-y');
            case 'daily':
                return Carbon::createFromFormat('Y-m-d', $period)->format('d-M-y');
            default:
                return $period;
        }
    }
}
