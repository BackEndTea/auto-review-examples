<?php

declare(strict_types=1);

namespace Art\AutoCodeReview\CommandStructure;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

use function class_exists;
use function str_replace;
use function substr;

class CommandStructureTest extends TestCase
{
    #[DataProvider('provideHandlerClasses')]
    public function testHandlerHasInvoke(ReflectionClass $handler): void
    {
        $invoke = $handler->getMethod('__invoke');
        self::assertTrue(
            $invoke->isPublic(),
            'Handler should have a public __invoke method, "' . $handler->getName() . '" does not.',
        );

        $parameters = $invoke->getParameters();
        self::assertCount(
            1,
            $parameters,
            'Handler should have exactly one parameter, "' . $handler->getName() . '" does not.',
        );
        $expectedParameterName = $handler->getNamespaceName() . '\\' . substr($handler->getShortName(), 0, -7);

        self::assertTrue(
            class_exists($expectedParameterName),
            'Commands should be defined next to their handler. Expected class "' . $expectedParameterName . '" does not exist.',
        );

        $parameter = $parameters[0];
        self::assertSame(
            $expectedParameterName,
            (string) $parameter->getType(),
            'Handler should have a parameter of the same type as the handler, "' . $handler->getName() . '" does not.',
        );
    }

    public static function provideHandlerClasses(): Generator
    {
        $finder = Finder::create()
            ->in(__DIR__ . '/../../../src/Commands')
            ->name('*Handler.php');

        foreach ($finder as $file) {
            $handlerClass = 'Art\\Commands\\' .
                str_replace('/', '\\', substr($file->getRelativePathname(), 0, -4));

            yield [new ReflectionClass($handlerClass)];
        }
    }

    #[DataProvider('provideCommandClasses')]
    public function testCommandsAreDefinedNextToHandlers(ReflectionClass $command): void
    {
        $handlerClass =  $command->getNamespaceName() . '\\' . $command->getShortName() . 'Handler';
        self::assertTrue(
            class_exists($handlerClass),
            'Commands should be defined next to their handler. Expected class "' . $handlerClass . '" does not exist.',
        );
    }

    public static function provideCommandClasses(): Generator
    {
        $finder = Finder::create()
            ->in(__DIR__ . '/../../../src/Commands')
            ->name('*.php')
            ->notName('*Handler.php');

        foreach ($finder as $file) {
            $handlerClass = 'Art\\Commands\\' .
                str_replace('/', '\\', substr($file->getRelativePathname(), 0, -4));

            yield [new ReflectionClass($handlerClass)];
        }
    }
}
