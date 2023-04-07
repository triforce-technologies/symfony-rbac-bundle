<?php

declare(strict_types=1);

namespace RbacBundle\Attribute\AccessControl;

use Attribute;
use RbacBundle\Core\RbacInterface;
use RbacBundle\Exception\RbacException;
use RbacBundle\Attribute\RBACAttributeInterface;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final class IsGranted implements RBACAttributeInterface
{
    public function __construct(
        public readonly string $permission = "",
        public readonly ?int $statusCode = 403,
        public readonly ?string $message = 'This resource is not allowed for the current user'
    ) {
    }

    public function getSecurityCheckMethod(RbacInterface $accessControl, mixed $userId): bool
    {
        try {
            return $accessControl->hasPermission($this->permission, $userId);
        } catch (RbacException) {
            return false;
        }
    }
}
