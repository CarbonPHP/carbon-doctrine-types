<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Carbon\CarbonImmutable;
use DateTimeInterface;
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

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format($platform->getDateTimeTzFormatString());
        }

        return $this->convertCarbonToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?CarbonImmutable
    {
        return $this->doConvertToPHPValue($value);
    }

    protected function getCarbonClassName(): string
    {
        return CarbonImmutable::class;
    }
}
