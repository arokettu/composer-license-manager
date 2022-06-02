<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager;

use Composer\Plugin\Capability\CommandProvider;

final class Commands implements CommandProvider
{
    public function getCommands(): array
    {
        return [];
    }
}
