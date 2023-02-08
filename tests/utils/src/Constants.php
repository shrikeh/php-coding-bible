<?php

declare(strict_types=1);

namespace Tests\Utils;

final class Constants
{

    public static function rootDir(): string
    {
        return dirname(__DIR__, 3);
    }

    public static function testsDir(): string
    {
        return self::rootDir() . '/tests';
    }

    public static function buildDir(): string
    {
        return (string) $_ENV['APP_BUILD_DIR'];
    }

    public static function srcDir(): string
    {
        return self::rootDir() . '/Shrikeh';
    }

    public static function fixturesDir(): string
    {
        return self::testsDir() . '/fixtures';
    }

    public static function vendorDir(): string
    {
        return self::rootDir() . '/vendor';
    }
}
