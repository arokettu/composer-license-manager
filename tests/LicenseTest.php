<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Tests;

use Arokettu\Composer\LicenseManager\Config\Config;
use Arokettu\Composer\LicenseManager\Helpers\LicenseHelper;
use Arokettu\Composer\LicenseManager\Tests\Models\MockPackage;
use PHPUnit\Framework\TestCase;

final class LicenseTest extends TestCase
{
    public function testDefaultConfig(): void
    {
        $config = Config::fromArray([]);

        // any license is allowed
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT']), $config));
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['(MIT AND LGPL-2.1)']), $config));
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['proprietary']), $config));
        // except for no license set
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', []), $config));
    }

    public function testBasicAllow(): void
    {
        $config = Config::fromArray(['licenses' => ['allowed' => 'MIT']]);

        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT']), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['BSD-2-Clause']), $config));
    }

    public function testBasicForbidden(): void
    {
        $config = Config::fromArray(['licenses' => ['forbidden' => 'MIT']]);

        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT']), $config));
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['BSD-2-Clause']), $config));
    }

    public function testGlobAllow(): void
    {
        $config = Config::fromArray(['licenses' => ['allowed' => 'LGPL-*']]);

        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['LGPL-2.1']), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['AGPL-3.0']), $config));
    }

    public function testGlobForbidden(): void
    {
        $config = Config::fromArray(['licenses' => ['forbidden' => 'AGPL-*']]);

        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['LGPL-2.1']), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['AGPL-3.0']), $config));
    }

    public function testGlobDoesNotAllowExpressions(): void
    {
        // with bracket, allow
        $config = Config::fromArray(['licenses' => ['allowed' => '(MIT*']]);
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['(MIT AND LGPL-2.1)']), $config));

        // no bracket, allow
        $config = Config::fromArray(['licenses' => ['allowed' => 'MIT*']]);
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT AND LGPL-2.1']), $config));

        // with bracket, forbid
        $config = Config::fromArray(['licenses' => ['forbidden' => '(MIT*']]);
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['(MIT AND LGPL-2.1)']), $config));

        // no bracket, forbid
        $config = Config::fromArray(['licenses' => ['forbidden' => 'MIT*']]);
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT AND LGPL-2.1']), $config));

        // but exact is ok
        $config = Config::fromArray(['licenses' => ['allowed' => 'MIT AND LGPL-2.1']]);
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT AND LGPL-2.1']), $config));
        $config = Config::fromArray(['licenses' => ['forbidden' => 'MIT AND LGPL-2.1']]);
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT AND LGPL-2.1']), $config));
    }

    public function testEmptyLicense(): void
    {
        $config = Config::fromArray(['licenses' => ['allow-empty' => true]]);
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', []), $config));

        $config = Config::fromArray(['licenses' => ['allow-empty' => false]]);
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/bar', []), $config));
    }

    public function testPackageExceptions(): void
    {
        $config = Config::fromArray([
            'licenses' => [
                'allow-empty' => false,
                'forbidden' => ['LGPL-*', 'MIT'],
            ],
            'packages' => [
                'allowed' => ['foo/bar', 'mypackage/*'],
            ],
        ]);

        // overrides empty
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', []), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/baz', []), $config));

        // overrides exact
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['MIT']), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/baz', ['MIT']), $config));

        // overrides glob
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('foo/bar', ['LGPL-2.1']), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('foo/baz', ['LGPL-2.1']), $config));

        // by package glob too
        self::assertTrue(LicenseHelper::isPermitted(new MockPackage('mypackage/demo', ['MIT']), $config));
        self::assertFalse(LicenseHelper::isPermitted(new MockPackage('notmine/demo', ['MIT']), $config));
    }
}
