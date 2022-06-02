<?php

namespace Arokettu\Composer\LicenseManager\Helpers;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\LicenseManagerPlugin;
use Composer\Package\CompletePackageInterface;

class LicenseHelper
{
    public static function isPermitted(CompletePackageInterface $package, Config $config, bool $isDev): bool
    {
        if ($package->getName() === LicenseManagerPlugin::PACKAGE) {
            return true;
        }

        return false;
    }
}
