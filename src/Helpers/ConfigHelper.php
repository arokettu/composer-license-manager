<?php

/**
 * @copyright 2022 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Helpers;

/**
 * @internal
 */
final class ConfigHelper
{
    public static function valueToArray(mixed $value): array
    {
        if (!\is_array($value)) {
            $value = [$value];
        }

        if (!array_is_list($value)) {
            throw new \InvalidArgumentException('Config array must be a list');
        }

        foreach ($value as $v) {
            if (!\is_string($v)) {
                throw new \InvalidArgumentException('Config array must be a list of strings');
            }
        }

        return $value;
    }

    /**
     * @param array<list<string>> $list
     */
    public static function isInList(string $value, array $list): bool
    {
        return self::isInPlainList($value, $list) || self::isInGlobList($value, $list);
    }

    /**
     * @param array<list<string>> $list
     */
    public static function isInPlainList(string $value, array $list): bool
    {
        return \in_array($value, $list['list']);
    }

    /**
     * @param array<list<string>> $list
     */
    public static function isInGlobList(string $value, array $list): bool
    {
        foreach ($list['glob'] as $prefix) {
            if ($prefix === '') {
                return true; // for glob '*' allow everything including expressions
            }

            if (str_contains($value, ' ')) {
                return false; // expressions are never covered by glob
            }

            if (str_starts_with($value, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
