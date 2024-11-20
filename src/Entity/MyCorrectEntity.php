<?php

namespace Art\Entity;

use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Uid\Uuid;

#[HasLifecycleCallbacks]
class MyCorrectEntity
{
    use TimestampableEntity;

    public Uuid $id;
}