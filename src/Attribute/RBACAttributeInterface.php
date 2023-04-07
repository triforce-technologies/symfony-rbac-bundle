<?php

declare(strict_types=1);

namespace RbacBundle\Attribute;

use Attribute;
use RbacBundle\Core\RbacInterface;

#[Attribute]
interface RBACAttributeInterface
{
    public function getSecurityCheckMethod(RbacInterface $accessControl, mixed $userId): bool;
}
