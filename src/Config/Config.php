<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Config;

use Arokettu\Composer\LicenseManager\Helpers\ConfigHelper;
use Arokettu\Composer\LicenseManager\LicenseManagerPlugin;
use Composer\Composer;

/**
 * @internal
 */
final class Config
{
    private $licensesAllowed;
    private $licensesForbidden;
    private $licensesAllowedDev;
    private $packagesAllowed;

    public static function fromComposer(Composer $composer): self
    {
        $extra = $composer->getPackage()->getExtra();
        $config = $extra[LicenseManagerPlugin::PACKAGE] ?? [];

        if (!\is_array($config)) {
            throw new \RuntimeException('Invalid config format: License Manager config must be an array');
        }

        return new self(
            ConfigHelper::valueToArray($config['licenses']['allowed'] ?? ['*']),
            ConfigHelper::valueToArray($config['licenses']['forbidden'] ?? []),
            ConfigHelper::valueToArray($config['licenses']['allowed-dev'] ?? []),
            ConfigHelper::valueToArray($config['packages']['allowed'] ?? [])
        );
    }

    private function __construct(
        array $licensesAllowed,
        array $licensesForbidden,
        array $licensesAllowedDev,
        array $packagesAllowed
    ) {
        $this->licensesAllowed = $licensesAllowed;
        $this->licensesForbidden = $licensesForbidden;
        $this->licensesAllowedDev = $licensesAllowedDev;
        $this->packagesAllowed = $packagesAllowed;
    }

    public function getLicensesAllowed(): array
    {
        return $this->licensesAllowed;
    }

    public function getLicensesForbidden(): array
    {
        return $this->licensesForbidden;
    }

    public function getLicensesAllowedDev(): array
    {
        return $this->licensesAllowedDev;
    }

    public function getPackagesAllowed(): array
    {
        return $this->packagesAllowed;
    }
}
