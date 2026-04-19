<?php

/**
 * @copyright 2022 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Helpers;

use Arokettu\Composer\LicenseManager\Config\Config;
use Composer\Package\CompletePackageInterface;

/**
 * @internal
 */
final class LicenseHelper
{
    public static function isPermitted(CompletePackageInterface $package, Config $config): bool
    {
        if (ConfigHelper::isInList($package->getName(), $config->getPackagesAllowed())) {
            return true;
        }

        $licenses = $package->getLicense();

        if ($licenses === []) {
            return $config->isEmptyLicenseAllowed();
        }

        $allowed = $config->getLicensesAllowed();
        $forbidden = $config->getLicensesForbidden();

        foreach ($licenses as $license) {
            if (!\is_string($license)) {
                $license = $license['name'] ?? null;
                if ($license === null || !\is_string($license)) {
                    throw new \Error(\sprintf(
                        'Invalid license declaration %s found in package %s',
                        json_encode($licenses, \JSON_THROW_ON_ERROR),
                        $package->getName(),
                    ));
                }
            }
            $license = strtolower(trim($license));

            if (ConfigHelper::isInPlainList($license, $forbidden)) {
                continue; // blacklisted by name
            }

            if (ConfigHelper::isInPlainList($license, $allowed)) {
                return true; // whitelisted by name
            }

            if (ConfigHelper::isInGlobList($license, $forbidden)) {
                continue; // blacklisted by glob
            }

            if (ConfigHelper::isInGlobList($license, $allowed)) {
                return true; // whitelisted by glob
            }
        }

        return false;
    }
}
