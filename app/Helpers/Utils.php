<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class Utils
{
    // common function
    public static function constructDates(string $startDate, string $endDate): Collection
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = collect();

        foreach ($period as $date) {
            $dates->push($date->format('Y-m-d'));
        }
        return $dates;
    }

    public static function constructPeriodToDate($period, $dateGroup, ?string $startDate = null)
    {
        switch ($dateGroup) {
            case 'yearly':
                return $period;
            case 'monthly':
                return Carbon::createFromFormat('Y-m', $period)->format('M-y');
            case 'weekly':
                [$year, $week] = explode('-', $period);
                $weekStart = Carbon::now()->setISODate($year, $week)->startOfWeek(Carbon::MONDAY);
                // compare with startDate if weekStart go before filtered startDate, take startDate
                $startDateFormatted = $startDate !== null ? Carbon::parse($startDate) : Carbon::now();
                if ($startDate !== null && $weekStart->lt($startDateFormatted)) {
                    return $startDateFormatted->format('d-M-y');
                }
                return $weekStart->format('d-M-y');
            case 'daily':
                return Carbon::createFromFormat('Y-m-d', $period)->format('d-M-y');
            default:
                return $period;
        }
    }

    public static function constructPeriodToDateISO($period, $dateGroup, ?string $startDate = null)
    {
        switch ($dateGroup) {
            case 'yearly':
                return $period;
            case 'monthly':
                return Carbon::createFromFormat('Y-m', $period)->toDateString();
            case 'weekly':
                [$year, $week] = explode('-', $period);
                $weekStart = Carbon::now()->setISODate($year, $week)->startOfWeek(Carbon::MONDAY);
                // compare with startDate if weekStart go before filtered startDate, take startDate
                $startDateFormatted = $startDate !== null ? Carbon::parse($startDate) : Carbon::now();
                if ($startDate !== null && $weekStart->lt($startDateFormatted)) {
                    return $startDateFormatted->toDateString();
                }
                return $weekStart->toDateString();
            case 'daily':
                return Carbon::createFromFormat('Y-m-d', $period)->toDateString();
            default:
                return $period;
        }
    }

    public static function constructDateToPeriod($date, $dateGroup)
    {
        switch ($dateGroup) {
            case 'yearly':
                return Carbon::parse($date)->format('Y');
            case 'monthly':
                return Carbon::parse($date)->format('Y-m');
            case 'weekly':
                return Carbon::parse($date)->format('o-W');
            case 'daily':
            default:
                return Carbon::parse($date)->format('Y-m-d');
        }
    }

    public static function formatCarbonDateWithDot($date)
    {
        if (!empty($date) && $date !== "00.00.0000") {
            return Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
        }
        return null;
    }

    public static function formatCarbonDateYmdToDotDmy($date)
    {
        if (!empty($date) && $date !== "00000000") {
            return Carbon::createFromFormat('Ymd', $date)->format('d.m.Y');
        }
        return null;
    }

    public static function formatCarbonDateToDashYmd($date)
    {
        if (!empty($date) && $date !== "00000000") {
            return Carbon::createFromFormat('Ymd', $date)->format('Y-m-d');
        }
        return null;
    }

    public static function formatRawTime($rawTime)
    {
        if (!str_contains($rawTime, ':')) {
            return implode(':', str_split($rawTime, 2));
        }
        return $rawTime;
    }

    public static function formatCarbonDateTime($date, $time)
    {
        $formattedTime = Utils::formatRawTime($time);
        if (!empty($date) && !empty($time)) {
            return Carbon::createFromFormat('d.m.Y H:i:s', "$date $formattedTime")->format('n/j/y h:i A');
        }
    }

    public static function getSqlDateFormatByDateGroup(string $dateGroup)
    {
        switch ($dateGroup) {
            case 'yearly':
                $dateFormat = "YYYY";
                break;
            case 'monthly':
                $dateFormat = "YYYY-MM";
                break;
            case 'weekly':
                $dateFormat = "IYYY-IW"; // ISO year + week format
                break;
            case 'daily':
            default:
                $dateFormat = "YYYY-MM-DD";
                break;
        }
        return $dateFormat;
    }
}
