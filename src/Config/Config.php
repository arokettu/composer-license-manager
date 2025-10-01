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
    /** @var array<list<string>> */
    private array $licensesAllowed;
    /** @var array<list<string>> */
    private array $licensesForbidden;
    /** @var array<list<string>> */
    private array $packagesAllowed;

    public static function fromComposer(Composer $composer): self
    {
        $extra = $composer->getPackage()->getExtra();
        $config = $extra[LicenseManagerPlugin::PACKAGE] ?? [];

        if (!\is_array($config)) {
            throw new \RuntimeException('Invalid config format: License Manager config must be an array');
        }

        return self::fromArray($config);
    }

    public static function fromArray(array $config): self
    {
        $licenses = $config['licenses'] ?? [];
        if ($licenses !== [] && array_is_list($licenses)) {
            $licenses = ['allowed' => $licenses];
        }

        $packages = $config['packages'] ?? [];
        if ($packages !== [] && array_is_list($packages)) {
            $packages = ['allowed' => $packages];
        }

        return new self(
            ConfigHelper::valueToArray($licenses['allowed'] ?? ['*']),
            ConfigHelper::valueToArray($licenses['forbidden'] ?? []),
            ConfigHelper::valueToArray($packages['allowed'] ?? []),
            $licenses['allow-empty'] ?? false,
            $config['enforced'] ?? true,
        );
    }

    private function __construct(
        array $licensesAllowed,
        array $licensesForbidden,
        array $packagesAllowed,
        private readonly bool $allowEmptyLicense,
        private readonly bool $enforced,
    ) {
        $this->licensesAllowed = $this->normalizeAndSplitGlobs($licensesAllowed);
        $this->licensesForbidden = $this->normalizeAndSplitGlobs($licensesForbidden);
        $this->packagesAllowed = $this->normalizeAndSplitGlobs($packagesAllowed);
    }

    /**
     * @return array<list<string>>
     */
    public function getLicensesAllowed(): array
    {
        return $this->licensesAllowed;
    }

    /**
     * @return array<list<string>>
     */
    public function getLicensesForbidden(): array
    {
        return $this->licensesForbidden;
    }

    /**
     * @return array<list<string>>
     */
    public function getPackagesAllowed(): array
    {
        return $this->packagesAllowed;
    }

    /**
     * @return array<list<string>>
     */
    private function normalizeAndSplitGlobs(array $list): array
    {
        return array_reduce($list, static function (array $carry, string $item) {
            $item = strtolower($item); // normalize to lowercase
            /** @var array<list<string>> $carry */
            if (str_ends_with($item, '*')) {
                $carry['glob'][] = substr($item, 0, -1);
            } else {
                $carry['list'][] = $item;
            }

            return $carry;
        }, ['list' => [], 'glob' => []]);
    }

    public function isEmptyLicenseAllowed(): bool
    {
        return $this->allowEmptyLicense;
    }

    public function isEnforced(): bool
    {
        return $this->enforced;
    }
}
