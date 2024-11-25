<?php

declare(strict_types=1);

namespace Art\AutoCodeReview;

use Art\Entity\TimestampableEntity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Uid\Uuid;

use function array_map;

final class EntityTest extends TestCase
{
    #[DataProvider('provideEntities')]
    public function testEntitiesUseUuids(ReflectionClass $rc): void
    {
        $id = $rc->getProperty('id');
        self::assertSame(
            Uuid::class,
            (string) $id->getType(),
            'Entities should use Uuids as id, "' . $rc->getName() . '" does not.',
        );
    }

    #[DataProvider('provideEntities')]
    public function testEntitiesHaveTimestamps(ReflectionClass $rc): void
    {
        $traitNames = array_map(static fn (ReflectionClass $trait) => $trait->getName(), $rc->getTraits());

        self::assertContains(
            TimestampableEntity::class,
            $traitNames,
            'Entities should be timestampable, "' . $rc->getName() . '" is not.',
        );
    }

    #[DataProvider('provideEntities')]
    public function testEntitiesHaveLifecycleCallbacks(ReflectionClass $rc): void
    {
        self::assertNotEmpty(
            $rc->getAttributes(HasLifecycleCallbacks::class),
            'Entities should have lifecycle callbacks, to make the timestamps work, "' . $rc->getName() . '" does not.',
        );
    }

    public static function provideEntities(): Generator
    {
        $entities = Finder::create()
            ->files()
            ->in(__DIR__ . '/../../src/Entity')
            ->name('*.php');

        foreach ($entities as $entity) {
            $rc = new ReflectionClass('Art\\Entity\\' . $entity->getFilenameWithoutExtension());
            if ($rc->isTrait()) {
                continue;
            }

            yield $rc->getName() => [$rc];
        }
    }
}
