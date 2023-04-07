<?php

declare(strict_types=1);

namespace Triforce\RBACBundle\Attribute\AccessControl;

use Attribute;
use Triforce\RBACBundle\Attribute\RBACAttributeInterface;
use Triforce\RBACBundle\Core\RbacInterface;
use Triforce\RBACBundle\Exception\RbacException;

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
