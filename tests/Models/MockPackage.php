<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Tests\Models;

use Composer\Package\CompletePackage;

class MockPackage extends CompletePackage
{
    public function __construct(string $name, array $licenses)
    {
        $this->name = $name;
        $this->license = $licenses;
    }
}
