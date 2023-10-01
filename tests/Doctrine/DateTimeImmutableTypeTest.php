<?php

declare(strict_types=1);

namespace Doctrine;

use Carbon\CarbonImmutable;
use Carbon\Doctrine\CarbonImmutableType;
use Carbon\Doctrine\DateTimeImmutableType;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class DateTimeImmutableTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(\Doctrine\DBAL\Types\VarDateTimeImmutableType::class)) {
            self::markTestSkipped('DateTimeImmutable unsupported');
        }
    }

    public function testDateTimeImmutableType(): void
    {
        Type::overrideType('datetime_immutable', DateTimeImmutableType::class);
        /** @var DateTimeImmutableType $type */
        $type = Type::getType('datetime_immutable');
        $platformClass = class_exists(MySQLPlatform::class)
            ? MySQLPlatform::class
            : \Doctrine\DBAL\Platforms\MySqlPlatform::class;
        $platform = new $platformClass();

        self::assertInstanceOf(DateTimeImmutableType::class, $type);
        self::assertNull($type->convertToPHPValue(null, $platform));

        $date = $type->convertToPHPValue(new DateTimeImmutable('2000-01-01 12:00 UTC'), $platform);

        self::assertInstanceOf(CarbonImmutable::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));

        $date = $type->convertToPHPValue('2000-01-01 12:00 UTC', $platform);

        self::assertInstanceOf(CarbonImmutable::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));
    }
}
