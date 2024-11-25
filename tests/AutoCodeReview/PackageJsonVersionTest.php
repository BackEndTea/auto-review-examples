<?php

declare(strict_types=1);

namespace Art\AutoCodeReview;

use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function json_decode;

class PackageJsonVersionTest extends TestCase
{
    public function testPackageLockJsonVersionDidntChange(): void
    {
        $packageLockJson = file_get_contents(__DIR__ . '/../../package-lock.json');

        $packageLockJsonVersion = json_decode($packageLockJson, true)['lockfileVersion'];

        self::assertSame(
            3,
            $packageLockJsonVersion,
            'Package-lock.json version has changed. This means you updated dependencies with the wrong npm version.',
        );
    }
}
