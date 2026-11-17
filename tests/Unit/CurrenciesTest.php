<?php

namespace Tests\Unit;

use App\Support\Currencies;
use PHPUnit\Framework\TestCase;

class CurrenciesTest extends TestCase
{
    public function test_format_without_float_rounding(): void
    {
        $this->assertSame("1'234'567.50 CHF", Currencies::format('1234567.50', 'CHF'));
        $this->assertSame('0.10 EUR', Currencies::format('0.1', 'EUR'));
        $this->assertSame('-950.10 CHF', Currencies::format('-950.10', 'CHF'));
        $this->assertSame('–', Currencies::format(null, 'CHF'));
    }
}
