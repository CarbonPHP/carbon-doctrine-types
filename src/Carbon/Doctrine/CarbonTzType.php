<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzType;

class CarbonTzType extends DateTimeTzType implements CarbonDoctrineType
{
    use CarbonTypeConverter;

    public function getName(): string
    {
        return 'carbontz';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $this->convertCarbonToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CarbonInterface
    {
        return $this->convertToCarbon($value, $platform);
    }

    protected function getClassName(): string
    {
        return Carbon::class;
    }
}