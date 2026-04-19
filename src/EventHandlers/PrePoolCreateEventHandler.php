<?php

/**
 * @copyright 2022 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\EventHandlers;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\Helpers\LicenseHelper;
use Arokettu\Composer\LicenseManager\LicenseManagerPlugin;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PrePoolCreateEvent;

/**
 * @internal
 */
final class PrePoolCreateEventHandler
{
    public function __construct(
        private readonly Composer $composer,
        private readonly IOInterface $io,
    ) {
    }

    public function handle(PrePoolCreateEvent $event): void
    {
        $config = Config::fromComposer($this->composer);
        $rootPackage = $this->composer->getPackage()->getName();
        /** @var array<string, array<int, string>> $filteredList */
        $filteredList = [];
        /** @var array<BasePackage> $filteredList */
        $filteredPackages = [];

        $packages = array_filter(
            $event->getPackages(),
            static function (PackageInterface $package) use (
                &$filteredList,
                &$filteredPackages,
                $config,
                $rootPackage,
            ) {
                $packageName = $package->getName();

                if (
                    $packageName === $rootPackage || // do not block root package
                    $packageName === LicenseManagerPlugin::PACKAGE || // do not block the manager itself
                    !str_contains($packageName, '/') // platform and composer packages
                ) {
                    return true;
                }

                if ($package instanceof CompletePackageInterface) {
                    if (LicenseHelper::isPermitted($package, $config)) {
                        return true;
                    } else {
                        $filteredList[$packageName] = $package->getLicense(); // display only once
                        $filteredPackages[] = $package;
                        return false;
                    }
                } else {
                    throw new \LogicException('Filtering can work only on complete packages');
                }
            },
        );

        if ($filteredList !== []) {
            $this->io->write('<warning>Some packages do not conform to the license policy:</warning>');
            $idx = 1;
            foreach ($filteredList as $package => $licenses) {
                $license = $licenses === [] ? '(no license set)' : implode(' | ', $licenses);
                $this->io->write("<warning>{$idx}. {$package}: {$license}</warning>");
                ++$idx;
            }
        }

        if ($config->isEnforced()) {
            $event->setPackages($packages);
            $event->setUnacceptableFixedPackages(array_unique(array_merge(
                $event->getUnacceptableFixedPackages(),
                array_intersect($filteredPackages, $event->getRequest()->getFixedOrLockedPackages()),
            )));
        }
    }
}
