<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzType;

class CarbonTzType extends DateTimeTzType implements CarbonDoctrineType
{
    use CarbonTypeConverter
    {
        convertToDatabaseValue as convertCarbonToDatabaseValue;
    }

    public function getName(): string
    {
        return 'carbontz';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format($platform->getDateTimeTzFormatString());
        }

        return $this->convertCarbonToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Carbon
    {
        return $this->doConvertToPHPValue($value);
    }
}
