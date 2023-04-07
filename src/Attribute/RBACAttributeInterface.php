<?php

declare(strict_types=1);

namespace TriforceRbacBundle\Attribute;

use Attribute;
use TriforceRbacBundle\Core\RbacInterface;

#[Attribute]
interface RBACAttributeInterface
{
    public function getSecurityCheckMethod(RbacInterface $accessControl, mixed $userId): bool;
}
