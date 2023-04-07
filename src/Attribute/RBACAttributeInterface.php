<?php

declare(strict_types=1);

namespace Triforce\RBACBundle\Attribute;

use Attribute;
use Triforce\RBACBundle\Core\RbacInterface;

#[Attribute]
interface RBACAttributeInterface
{
    public function getSecurityCheckMethod(RbacInterface $accessControl, mixed $userId): bool;
}
