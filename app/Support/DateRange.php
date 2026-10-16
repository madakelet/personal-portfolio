<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Formats CV date ranges in the current locale. Dates are "YYYY" or
 * "YYYY-MM"; a missing end date means "present".
 */
class DateRange
{
    public static function format(string $from, ?string $to = null): string
    {
        $end = $to === null ? __('ui.dates.present') : self::point($to);

        return self::point($from).' – '.$end;
    }

    private static function point(string $date): string
    {
        if (strlen($date) === 4) {
            return $date;
        }

        return Carbon::createFromFormat('!Y-m', $date)
            ->locale(app()->getLocale())
            ->isoFormat(__('ui.dates.month'));
    }
}
