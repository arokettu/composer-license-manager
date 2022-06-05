<?php

declare(strict_types=1);

namespace Arokettu\Composer\LicenseManager\Tests;

use Arokettu\Composer\LicenseManager\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testEnforceBoolean(): void
    {
        $this->expectException(\TypeError::class);

        Config::fromArray(['licenses' => ['allow-empty' => 0]]);
    }

    public function testEnforceListOfStrings(): void
    {
        // string is allowed
        self::assertInstanceOf(Config::class, Config::fromArray(['licenses' => ['allowed' => 'MIT']]));
        // list array is allowed
        self::assertInstanceOf(Config::class, Config::fromArray(['licenses' => ['forbidden' => ['MIT']]]));
    }

    public function testEnforceListOfStringsMustBeString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Config::fromArray(['licenses' => ['allowed' => 123]]);
    }

    public function testEnforceListOfStringsMustBeStringInList(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Config::fromArray(['licenses' => ['allowed' => ['MIT', 123]]]);
    }

    public function testEnforceListOfStringsMustBeAList(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Config::fromArray(['licenses' => ['allowed' => ['MIT' => 'MIT', 123 => 123]]]);
    }
}
