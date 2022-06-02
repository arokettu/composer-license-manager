<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Helpers;

use Arokettu\Composer\LicenseManager\Config\Config;
use Composer\Package\CompletePackageInterface;

/**
 * @internal
 */
class LicenseHelper
{
    public static function isPermitted(CompletePackageInterface $package, Config $config, bool $isDev): bool
    {
        if (ConfigHelper::isInList($package->getName(), $config->getPackagesAllowed())) {
            return true;
        }

        foreach ($package->getLicense() as $license) {
            if (str_starts_with($license, '(')) {
                continue; // license expressions are not supported for now
            }

            if ($isDev && ConfigHelper::isInList($license, $config->getLicensesAllowedDev())) {
                return true; // whitelisted for dev
            }

            if (ConfigHelper::isInList($license, $config->getLicensesForbidden())) {
                continue; // blacklisted
            }

            if (ConfigHelper::isInList($license, $config->getLicensesAllowed())) {
                return true; // whitelisted
            }
        }

        return false;
    }
}
