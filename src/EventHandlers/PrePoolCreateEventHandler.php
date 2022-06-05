<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\EventHandlers;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\Helpers\LicenseHelper;
use Arokettu\Composer\LicenseManager\LicenseManagerPlugin;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PrePoolCreateEvent;

/**
 * @internal
 */
class PrePoolCreateEventHandler
{
    /** @var Composer */
    private $composer;
    /** @var IOInterface */
    private $io;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function handle(PrePoolCreateEvent $event): void
    {
        $config = Config::fromComposer($this->composer);
        /** @var array<string, array<int, string>> $filtered */
        $filtered = [];

        $packages = array_filter($event->getPackages(), function (PackageInterface $package) use (&$filtered, $config) {
            $packageName = $package->getName();

            if ($packageName === LicenseManagerPlugin::PACKAGE || !str_contains($packageName, '/')) {
                return true;
            }

            if ($package instanceof CompletePackageInterface) {
                if (LicenseHelper::isPermitted($package, $config)) {
                    return true;
                } else {
                    $filtered[$packageName] = $package->getLicense();
                    return false;
                }
            } else {
                throw new \LogicException('Filtering can work only on complete packages');
            }
        });

        if ($filtered !== []) {
            $this->io->write('<warning>Some packages do not conform to the license policy:</warning>');
            $idx = 1;
            foreach ($filtered as $package => $licenses) {
                $license = $licenses === [] ? '(no license set)' : implode(' | ', $licenses);
                $this->io->write("<warning>{$idx}. {$package}: {$license}</warning>");
                ++$idx;
            }
        }

        if ($config->isEnforced()) {
            $event->setPackages($packages);
        }
    }
}
