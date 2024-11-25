<?php

declare(strict_types=1);

namespace Art\Entity;

use Symfony\Component\Uid\Uuid;

class MyEntityWithIdAndTimestamps
{
    use TimestampableEntity;

    public Uuid $id;
}
