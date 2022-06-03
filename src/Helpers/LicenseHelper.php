<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Helpers;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\LicenseManagerPlugin;
use Composer\Package\CompletePackageInterface;

/**
 * @internal
 */
class LicenseHelper
{
    public static function isPermitted(CompletePackageInterface $package, Config $config): bool
    {
        if (ConfigHelper::isInList($package->getName(), $config->getPackagesAllowed())) {
            return true;
        }

        $allowed = $config->getLicensesAllowed();
        $forbidden = $config->getLicensesForbidden();

        foreach ($package->getLicense() as $license) {
            if (str_starts_with($license, '(')) {
                continue; // license expressions are not supported for now
            }

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
