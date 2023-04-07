<?php

declare(strict_types=1);

namespace TriforceRbacBundle\Attribute\AccessControl;

use Attribute;
use TriforceRbacBundle\Core\RbacInterface;
use TriforceRbacBundle\Attribute\RBACAttributeInterface;
use TriforceRbacBundle\Exception\RbacException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class HasRole implements RBACAttributeInterface
{
    public function __construct(
        public string  $role = "",
        public ?int    $statusCode = 403,
        public ?string $message = 'This resource is not allowed for the current user'
    ) {
    }

    public function getSecurityCheckMethod(RbacInterface $accessControl, mixed $userId): bool
    {
        try {
            return $accessControl->hasRole($this->role, $userId);
        } catch (RbacException) {
            return false;
        }
    }
}
