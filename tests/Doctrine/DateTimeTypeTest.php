<?php

declare(strict_types=1);

namespace Doctrine;

use Carbon\Carbon;
use Carbon\Doctrine\DateTimeType;
use DateTime;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class DateTimeTypeTest extends TestCase
{
    public function testDateTimeType(): void
    {
        Type::overrideType('datetime', DateTimeType::class);
        /** @var DateTimeType $type */
        $type = Type::getType('datetime');
        $platformClass = class_exists(MySQLPlatform::class)
            ? MySQLPlatform::class
            : \Doctrine\DBAL\Platforms\MySqlPlatform::class;
        $platform = new $platformClass();

        self::assertInstanceOf(DateTimeType::class, $type);
        self::assertNull($type->convertToPHPValue(null, $platform));

        $date = $type->convertToPHPValue(new DateTime('2000-01-01 12:00 UTC'), $platform);

        self::assertInstanceOf(Carbon::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));

        $date = $type->convertToPHPValue('2000-01-01 12:00 UTC', $platform);

        self::assertInstanceOf(Carbon::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));
    }
}
