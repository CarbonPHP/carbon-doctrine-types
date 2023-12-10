<?php

declare(strict_types=1);

namespace Doctrine;

use Carbon\CarbonImmutable;
use Carbon\Doctrine\CarbonImmutableType;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\DB2Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class CarbonImmutableTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(\Doctrine\DBAL\Types\VarDateTimeImmutableType::class)) {
            self::markTestSkipped('DateTimeImmutable unsupported');
        }
    }

    public function testCarbonImmutableType(): void
    {
        $type = self::getType();
        $platformClass = class_exists(MySQLPlatform::class)
            ? MySQLPlatform::class
            : \Doctrine\DBAL\Platforms\MySqlPlatform::class;
        $platform = new $platformClass();

        self::assertInstanceOf(CarbonImmutableType::class, $type);
        self::assertTrue(
            $type->external ?? false,
            'carbonphp/carbon-doctrine-types autoload must take precedence'
        );
        self::assertNull($type->convertToPHPValue(null, $platform));

        $date = $type->convertToPHPValue(new DateTimeImmutable('2000-01-01 12:00 UTC'), $platform);

        self::assertInstanceOf(CarbonImmutable::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));

        $date = $type->convertToPHPValue('2000-01-01 12:00 UTC', $platform);

        self::assertInstanceOf(CarbonImmutable::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));
    }

    public function testPrecisionPerPlatform(): void
    {
        $type = self::getType();

        self::assertSame('DATETIME(3)', $type->getSQLDeclaration([
            'precision' => 99,
        ], new SQLitePlatform()));

        self::assertSame('DATETIME(6)', $type->getSQLDeclaration([
            'precision' => 99,
        ], new MySQLPlatform()));

        self::assertSame('TIMESTAMP(9)', $type->getSQLDeclaration([
            'precision' => 99,
        ], new OraclePlatform()));

        self::assertSame('TIMESTAMP(12)', $type->getSQLDeclaration([
            'precision' => 99,
        ], new DB2Platform()));
    }

    private static function getType(): CarbonImmutableType
    {
        if (!Type::hasType('carbon_immutable')) {
            Type::addType('carbon_immutable', CarbonImmutableType::class);
        }

        /** @var CarbonImmutableType $type */
        $type = Type::getType('carbon_immutable');

        return $type;
    }
}
