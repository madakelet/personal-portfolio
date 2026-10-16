<?php

namespace Tests\Unit;

use App\Support\DateRange;
use Tests\TestCase;

class DateRangeTest extends TestCase
{
    public function test_formats_years_months_and_open_ranges(): void
    {
        $this->app->setLocale('en');

        $this->assertSame('2017 – 2020', DateRange::format('2017', '2020'));
        $this->assertSame('Mar 2023 – Dec 2023', DateRange::format('2023-03', '2023-12'));
        $this->assertSame('2024 – present', DateRange::format('2024', null));
    }
}
