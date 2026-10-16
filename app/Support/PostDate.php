<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/** Post dates in the current locale, using the patterns in lang/{locale}/ui.php. */
class PostDate
{
    public static function full(Carbon $date): string
    {
        return $date->copy()->locale(app()->getLocale())->isoFormat(__('ui.dates.day'));
    }

    /** Without the year, for lists that are already grouped by year. */
    public static function short(Carbon $date): string
    {
        return $date->copy()->locale(app()->getLocale())->isoFormat(__('ui.dates.day_short'));
    }
}
