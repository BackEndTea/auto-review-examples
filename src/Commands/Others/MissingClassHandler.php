<?php

declare(strict_types=1);

namespace Art\Commands\Others;

use Art\Commands\WrongDirectory\MissingClass;

class MissingClassHandler
{
    public function __invoke(MissingClass $class): void
    {
    }
}
