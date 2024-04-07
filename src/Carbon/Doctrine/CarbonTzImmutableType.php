<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzImmutableType;

class CarbonTzImmutableType extends DateTimeTzImmutableType implements CarbonDoctrineType
{
    use CarbonTypeConverter
    {
        convertToDatabaseValue as convertCarbonToDatabaseValue;
    }

    public function getName(): string
    {
        return 'carbontz_immutable';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $this->convertCarbonToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CarbonImmutable
    {
        return $this->convertToCarbon($value, $platform);
    }

    protected function getClassName(): string
    {
        return CarbonImmutable::class;
    }
}