<?php

namespace Triforce\RBACBundle\Core;

use Triforce\RBACBundle\Core\Manager\PermissionManagerInterface;
use Triforce\RBACBundle\Core\Manager\RoleManagerInterface;
use Triforce\RBACBundle\Entity\PermissionInterface;
use Triforce\RBACBundle\Entity\RoleInterface;
use Webmozart\Assert\Assert;

class Rbac implements RbacInterface
{
    public function __construct(
        private readonly PermissionManagerInterface $permissionManager,
        private readonly RoleManagerInterface       $roleManager
    ) {
    }

    public function hasPermission(string|int|PermissionInterface $permission, mixed $userId): bool
    {
        Assert::notEmpty($userId);

        $permissionId = $permission;
        if (is_object($permission)) {
            $permissionId = $permission->getId();
        } elseif (is_string($permission)) {
            $permissionId = $this->permissionManager->getPathId($permission);
        }

        return $this->permissionManager->hasPermission($permissionId, $userId);
    }

    public function hasRole(string|int|RoleInterface $role, mixed $userId): bool
    {
        Assert::notEmpty($userId);

        $roleId = $role;
        if (is_object($role)) {
            $roleId = $role->getId();
        } elseif (is_string($role)) {
            $roleId = $this->roleManager->getPathId($role);
        }

        return $this->roleManager->hasRole($roleId, $userId);
    }
}
