<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

/**
 * @internal
 */
final class LicenseManagerPlugin implements PluginInterface, Capable
{
    public const PACKAGE = 'arokettu/composer-license-manager';

    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public function getCapabilities(): array
    {
        return [
            CommandProvider::class => Commands::class,
        ];
    }
}
