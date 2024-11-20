<?php

namespace Art\Entity;

use Symfony\Component\Uid\Uuid;

class MyEntityWithIdAndTimestamps
{
    use TimestampableEntity;

    public Uuid $id;
}