<?php

declare(strict_types=1);

namespace Tests\Doctrine;

use Carbon\Doctrine\DateTimeDefaultPrecision;
use PHPUnit\Framework\TestCase;

class DateTimeDefaultPrecisionTest extends TestCase
{
    public function testPrecision(): void
    {
        self::assertSame(6, DateTimeDefaultPrecision::get());

        DateTimeDefaultPrecision::set(9);

        self::assertSame(9, DateTimeDefaultPrecision::get());

        DateTimeDefaultPrecision::set(6);

        self::assertSame(6, DateTimeDefaultPrecision::get());
    }
}
