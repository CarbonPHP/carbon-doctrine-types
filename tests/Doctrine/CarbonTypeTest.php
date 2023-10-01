<?php

declare(strict_types=1);

namespace Tests\Doctrine;

use Carbon\Carbon;
use Carbon\Doctrine\CarbonType;
use DateTime;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class CarbonTypeTest extends TestCase
{
    public function testCarbonType(): void
    {
        [$type, $platform] = $this->getTypeAndPlatform();

        self::assertInstanceOf(CarbonType::class, $type);
        self::assertTrue(
            $type->external ?? false,
            'carbonphp/carbon-doctrine-types autoload must take precedence',
        );
        self::assertNull($type->convertToPHPValue(null, $platform));

        $date = $type->convertToPHPValue(new DateTime('2000-01-01 12:00 UTC'), $platform);

        self::assertInstanceOf(Carbon::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));

        $date = $type->convertToPHPValue('2000-01-01 12:00 UTC', $platform);

        self::assertInstanceOf(Carbon::class, $date);
        self::assertSame('2000-01-01 12:00:00 UTC', $date->format('Y-m-d H:i:s e'));

        self::assertNull($type->convertToDatabaseValue(null, $platform));

        self::assertSame(
            '2000-01-01 12:00:00.000000',
            $type->convertToDatabaseValue(new Carbon('2000-01-01 12:00 UTC'), $platform),
        );
    }

    public function testConvertToDatabaseValueFailure(): void
    {
        self::expectExceptionObject(InvalidType::new(
            42,
            CarbonType::class,
            ['null', 'DateTime', 'Carbon'],
        ));

        [$type, $platform] = $this->getTypeAndPlatform();
        $type->convertToDatabaseValue(42, $platform);
    }

    public function testConvertToPHPValueFailure(): void
    {
        self::expectExceptionObject(ValueNotConvertible::new(
            'bad date string',
            CarbonType::class,
            'Y-m-d H:i:s.u or any format supported by Carbon\\Carbon::parse()',
        ));

        [$type, $platform] = $this->getTypeAndPlatform();
        $type->convertToPHPValue('bad date string', $platform);
    }

    public function testGetSqlDeclaration(): void
    {
        [$type, $platform] = $this->getTypeAndPlatform();
        $declaration = $type->getSQLDeclaration([], $platform);

        self::assertSame('DATETIME(6)', $declaration);

        $declaration = $type->getSQLDeclaration(
            ['precision' => 0],
            $platform,
        );

        self::assertSame('DATETIME', $declaration);
    }

    public function testPostgreSQL(): void
    {
        if (!class_exists(PostgreSQLPlatform::class)) {
            self::markTestSkipped('PostgreSQLPlatform unsupported');
        }

        [$type] = $this->getTypeAndPlatform();

        $declaration = $type->getSQLDeclaration([], new PostgreSQLPlatform());

        self::assertSame('TIMESTAMP(6) WITHOUT TIME ZONE', $declaration);
    }

    private function getTypeAndPlatform(): array
    {
        if (!Type::hasType('carbon_mutable')) {
            Type::addType('carbon_mutable', CarbonType::class);
        }

        /** @var CarbonType $type */
        $type = Type::getType('carbon_mutable');
        $platformClass = class_exists(MySQLPlatform::class)
            ? MySQLPlatform::class
            : \Doctrine\DBAL\Platforms\MySqlPlatform::class;
        $platform = new $platformClass();

        return [$type, $platform];
    }
}
