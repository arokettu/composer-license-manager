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
    /** @var string[][] */
    private $licensesAllowed;
    /** @var string[][] */
    private $licensesForbidden;
    /** @var string[][] */
    private $licensesAllowedDev;
    /** @var string[][] */
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
        $this->licensesAllowed = $this->splitGlobs($licensesAllowed);
        $this->licensesForbidden = $this->splitGlobs($licensesForbidden);
        $this->licensesAllowedDev = $this->splitGlobs($licensesAllowedDev);
        $this->packagesAllowed = $this->splitGlobs($packagesAllowed);
    }

    /**
     * @return string[][]
     */
    public function getLicensesAllowed(): array
    {
        return $this->licensesAllowed;
    }

    /**
     * @return string[][]
     */
    public function getLicensesForbidden(): array
    {
        return $this->licensesForbidden;
    }

    /**
     * @return string[][]
     */
    public function getLicensesAllowedDev(): array
    {
        return $this->licensesAllowedDev;
    }

    /**
     * @return string[][]
     */
    public function getPackagesAllowed(): array
    {
        return $this->packagesAllowed;
    }

    /**
     * @return string[][]
     */
    private function splitGlobs(array $list): array
    {
        return array_reduce($list, static function (array $carry, string $item) {
            /** @var string[][] $carry */
            if (str_ends_with($item, '*')) {
                $carry['glob'][] = substr($item, 0, -1);
            } else {
                $carry['list'][] = $item;
            }

            return $carry;
        }, ['list' => [], 'glob' => []]);
    }
}
