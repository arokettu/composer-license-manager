<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Helpers;

/**
 * @internal
 */
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

    /**
     * @param string[][] $list
     */
    public static function isInList(string $value, array $list): bool
    {
        if (\in_array($value, $list['list'])) {
            return true;
        }

        foreach ($list['glob'] as $prefix) {
            if (str_starts_with($value, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
