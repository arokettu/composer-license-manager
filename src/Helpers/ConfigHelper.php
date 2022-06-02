<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Helpers;

final class ConfigHelper
{
    /**
     * @param mixed $value
     */
    public static function valueToArray($value): array
    {
        if (\is_array($value)) {
            return $value;
        }

        if (\is_scalar($value)) {
            return [\strval($value)];
        }

        throw new \RuntimeException('Unexpected values are present in the config');
    }
}
