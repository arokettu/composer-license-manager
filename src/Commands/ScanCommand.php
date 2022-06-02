<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Commands;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScanCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('license:scan');
        $this->setDescription('Asserts whether installed packages conform to the license policy.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
