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
    private $packagesAllowed;
    /** @var bool */
    private $allowEmptyLicense;
    /** @var bool */
    private $enforced;

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
        return new self(
            ConfigHelper::valueToArray($config['licenses']['allowed'] ?? ['*']),
            $config['licenses']['allow-empty'] ?? false,
            ConfigHelper::valueToArray($config['licenses']['forbidden'] ?? []),
            ConfigHelper::valueToArray($config['packages']['allowed'] ?? []),
            $config['enforced'] ?? true
        );
    }

    private function __construct(
        array $licensesAllowed,
        bool $allowEmptyLicense,
        array $licensesForbidden,
        array $packagesAllowed,
        bool $enforced
    ) {
        $this->licensesAllowed = $this->normalizeAndSplitGlobs($licensesAllowed);
        $this->licensesForbidden = $this->normalizeAndSplitGlobs($licensesForbidden);
        $this->packagesAllowed = $this->normalizeAndSplitGlobs($packagesAllowed);
        $this->allowEmptyLicense = $allowEmptyLicense;
        $this->enforced = $enforced;
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
    public function getPackagesAllowed(): array
    {
        return $this->packagesAllowed;
    }

    /**
     * @return string[][]
     */
    private function normalizeAndSplitGlobs(array $list): array
    {
        return array_reduce($list, static function (array $carry, string $item) {
            $item = strtolower($item); // normalize to lowercase
            /** @var string[][] $carry */
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

    /** @return bool */
    public function isEnforced(): bool
    {
        return $this->enforced;
    }
}
