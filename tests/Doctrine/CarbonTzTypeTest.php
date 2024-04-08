<?php

declare(strict_types=1);

namespace Carbon\Tests\Doctrine;

use Carbon\Carbon;
use Carbon\Doctrine\CarbonTzType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

class CarbonTzTypeTest extends TestCase
{
    protected function setUp(): void
    {
        if (!Type::hasType('carbontz')) {
            Type::addType('carbontz', CarbonTzType::class);
        }
    }

    public function testGetName(): void
    {
        $type = Type::getType('carbontz');
        $this->assertSame('carbontz', $type->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $type = Type::getType('carbontz');
        $platform = $this->createMock(AbstractPlatform::class);
        $platform->method('getDateTimeTzFormatString')->willReturn('Y-m-d H:i:s.u P');

        $carbon = Carbon::parse('2023-06-08 12:34:56.789000', 'Europe/Paris');
        $expectedDatabaseValue = '2023-06-08 12:34:56.789000 +02:00';

        $actualDatabaseValue = $type->convertToDatabaseValue($carbon, $platform);

        $this->assertSame($expectedDatabaseValue, $actualDatabaseValue);
    }

    public function testConvertToPHPValue(): void
    {
        $type = Type::getType('carbontz');
        $platform = $this->createMock(AbstractPlatform::class);

        $databaseValue = '2023-06-08 12:34:56.789000 +02:00';
        $expectedCarbon = Carbon::parse('2023-06-08 12:34:56.789000', 'Europe/Paris');

        $actualCarbon = $type->convertToPHPValue($databaseValue, $platform);


        $this->assertInstanceOf(Carbon::class, $actualCarbon);
        $this->assertEquals($expectedCarbon, $actualCarbon);
    }
}