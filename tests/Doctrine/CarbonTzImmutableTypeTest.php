<?php

declare(strict_types=1);

namespace Carbon\Tests\Doctrine;

use Carbon\CarbonImmutable;
use Carbon\Doctrine\CarbonTzImmutableType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CarbonTzImmutableTypeTest extends TestCase
{
    public static function getPlatformsSupportingDateTimeTz(): array
    {
        return [
            'PostgreSQL' => [new PostgreSQLPlatform()],
            'Oracle' => [new OraclePlatform()],
            'SQLServer' => [new SQLServerPlatform()],
        ];
    }

    protected function setUp(): void
    {
        if (!Type::hasType('carbontz_immutable')) {
            Type::addType('carbontz_immutable', CarbonTzImmutableType::class);
        }
    }

    public function testGetName(): void
    {
        $type = Type::getType('carbontz_immutable');
        self::assertSame('carbontz_immutable', $type->getName());
    }

    #[DataProvider('getPlatformsSupportingDateTimeTz')]
    public function testConvertToDatabaseValue(AbstractPlatform $platform): void
    {
        $type = Type::getType('carbontz_immutable');

        $carbon = CarbonImmutable::parse('2023-06-08 12:34:56.789000', 'Europe/Paris');
        $expectedDatabaseValue = $carbon->format($platform->getDateTimeTzFormatString());

        $actualDatabaseValue = $type->convertToDatabaseValue($carbon, $platform);

        self::assertSame($expectedDatabaseValue, $actualDatabaseValue);
    }

    #[DataProvider('getPlatformsSupportingDateTimeTz')]
    public function testConvertToPHPValue(AbstractPlatform $platform): void
    {
        $type = Type::getType('carbontz_immutable');

        $carbon = CarbonImmutable::parse('2023-06-08 12:34:56.789000', 'Europe/Paris');
        $databaseValue = $carbon->format($platform->getDateTimeTzFormatString());

        $date = $type->convertToPHPValue($databaseValue, $platform);

        self::assertInstanceOf(CarbonImmutable::class, $date);
        self::assertSame('2023-06-08 12:34:56 +02:00', $date->format('Y-m-d H:i:s e'));
    }
}
