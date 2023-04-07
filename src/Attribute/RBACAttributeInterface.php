<?php

declare(strict_types=1);

namespace SymfonyRbacBundle\Attribute;

use Attribute;
use SymfonyRbacBundle\Core\RbacInterface;

#[Attribute]
interface RBACAttributeInterface
{
    public function getSecurityCheckMethod(RbacInterface $accessControl, mixed $userId): bool;
}
