<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Commands;

use Composer\Package\CompletePackageInterface;
use Composer\Repository\RepositoryInterface;

/**
 * This logic was extracted from Composer 2.2
 * @see \Composer\Command\LicensesCommand
 * @internal
 */
trait PackageBucketTrait
{
    /**
     * Find package requires and child requires
     *
     * @param  array<string, CompletePackageInterface> $bucket
     * @return array<string, CompletePackageInterface>
     */
    private function filterRequiredPackages(
        RepositoryInterface $repo,
        CompletePackageInterface $package,
        array $bucket = []
    ): array {
        $requires = array_keys($package->getRequires());

        $packageListNames = array_keys($bucket);
        /** @var CompletePackageInterface[] $packages */
        $packages = array_filter(
            $repo->getPackages(),
            function ($package) use ($requires, $packageListNames) {
                if (!($package instanceof CompletePackageInterface)) {
                    throw new \LogicException('Complete packages are required');
                }
                return \in_array($package->getName(), $requires) && !\in_array($package->getName(), $packageListNames);
            }
        );

        $bucket = $this->appendPackages($packages, $bucket);

        foreach ($packages as $package) {
            $bucket = $this->filterRequiredPackages($repo, $package, $bucket);
        }

        return $bucket;
    }

    /**
     * Adds packages to the package list
     *
     * @param  CompletePackageInterface[]              $packages the list of packages to add
     * @param  array<string, CompletePackageInterface> $bucket   the list to add packages to
     * @return array<string, CompletePackageInterface>
     */
    private function appendPackages(array $packages, array $bucket): array
    {
        foreach ($packages as $package) {
            $bucket[$package->getName()] = $package;
        }

        return $bucket;
    }
}
