<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Commands;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\Helpers\LicenseHelper;
use Composer\Command\BaseCommand;
use Composer\Package\CompletePackageInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class ScanCommand extends BaseCommand
{
    use PackageBucketTrait;

    private const EXIT_SUCCESS = 0;
    private const EXIT_FAILURE = 1;

    protected function configure(): void
    {
        $this->setName('licenses:scan');
        $this->setDescription('Asserts whether installed packages conform to the license policy.');

        $this->addOption(
            'no-dev',
            null,
            InputOption::VALUE_NONE,
            'Disables search in require-dev packages.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composer = $this->getComposer();
        $io = $this->getIO();
        $config = Config::fromComposer($composer);

        $root = $composer->getPackage();
        $repo = $composer->getRepositoryManager()->getLocalRepository();

        $packages = $this->filterRequiredPackages($repo, $root);

        if ($input->getOption('no-dev')) {
            $packagesDev = [];
        } else {
            /** @var CompletePackageInterface[] $allPackages */
            $allPackages = $repo->getPackages();
            $packagesDev = $this->appendPackages($allPackages, array());
            $packagesDev = array_diff_key($packagesDev, $packages);
        }

        $exit = self::EXIT_SUCCESS;

        foreach ([[$packages, false], [$packagesDev, true]] as [$packageList, $isDev]) {
            foreach ($packageList as $package) {
                if (LicenseHelper::isPermitted($package, $config, $isDev) === false) {
                    $license = implode(', ', $package->getLicense());
                    $io->write("<error>{$package->getName()} has forbidden license: {$license}</error>");
                }
            }
        }

        return $exit;
    }
}
