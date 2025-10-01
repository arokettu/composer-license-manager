<?php

/**
 * @copyright 2022 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PrePoolCreateEvent;

/**
 * @internal
 * @psalm-suppress MissingConstructor
 */
final class LicenseManagerPlugin implements PluginInterface, Capable, EventSubscriberInterface
{
    public const PACKAGE = 'arokettu/composer-license-manager';

    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // unused
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // unused
    }

    public function getCapabilities(): array
    {
        return [
            CommandProvider::class => Commands::class,
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [PluginEvents::PRE_POOL_CREATE => 'handlePrePoolCreate'];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress TypeDoesNotContainType
     * @psalm-suppress RedundantPropertyInitializationCheck
     */
    public function handlePrePoolCreate(PrePoolCreateEvent $event): void
    {
        if (!isset($this->composer) || !isset($this->io)) {
            throw new \LogicException('Composer and IO must be initialized');
        }

        (new EventHandlers\PrePoolCreateEventHandler($this->composer, $this->io))->handle($event);
    }
}
