<?php

declare(strict_types=1);

namespace Art\AutoCodeReview\Lint;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

use function file_get_contents;
use function preg_match_all;

final class BashHasSetETest extends TestCase
{
    #[DataProvider('provideBashScripts')]
    public function testBashScriptHasSetE(string $file): void
    {
        $content = file_get_contents($file);

        $matches = (bool) preg_match_all('/^set -e/m', $content);

        self::assertTrue($matches, 'File ' . $file . ' does not contain "set -e" directive');
    }

    /** @return Generator<array{string}> */
    public static function provideBashScripts(): Generator
    {
        $files = Finder::create()
            ->name(['*.sh', '*.bash'])
            ->files()
            ->in(__DIR__ . '/../../../scripts')
            // Dont include files you dont want to
            ->notPath('allowed_failure.sh');

        foreach ($files as $file) {
            yield [$file->getRealPath()];
        }
    }
}
