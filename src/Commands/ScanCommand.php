<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Commands;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\Helpers\LicenseHelper;
use Composer\Command\BaseCommand;
use Composer\Package\CompletePackageInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class ScanCommand extends BaseCommand
{
    private const EXIT_SUCCESS = 0;
    private const EXIT_FAILURE = 1;

    protected function configure(): void
    {
        $this->setName('licenses:scan');
        $this->setDescription('Asserts whether installed packages conform to the license policy.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composer = $this->requireComposer();
        $io = $this->getIO();
        $config = Config::fromComposer($composer);

        $repo = $composer->getRepositoryManager()->getLocalRepository();
        /** @var CompletePackageInterface[] $packages */
        $packages = $repo->getPackages();

        $exit = self::EXIT_SUCCESS;

        foreach ($packages as $package) {
            if (LicenseHelper::isPermitted($package, $config) === false) {
                $license = implode(', ', $package->getLicense());
                $io->write("<error>{$package->getName()} has forbidden license: {$license}</error>");
                $exit = self::EXIT_FAILURE;
            }
        }

        if ($exit === self::EXIT_SUCCESS) {
            $io->write('<info>All licenses conform to your policy</info>');
        }

        return $exit;
    }
}
