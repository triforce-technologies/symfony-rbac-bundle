<?php

namespace TriforceRbacBundle\Core\Manager;

use TriforceRbacBundle\Entity\RoleInterface;
use TriforceRbacBundle\Exception\RbacException;
use TriforceRbacBundle\Repository\PermissionRepository;
use TriforceRbacBundle\Entity\PermissionInterface;

/**
 * @property PermissionRepository $repository
 */
class PermissionManager extends NodeManager implements PermissionManagerInterface
{
    public function __construct(PermissionRepository $permissionRepository)
    {
        parent::__construct($permissionRepository);
    }

    /**
     * @throws RbacException
     */
    public function remove(PermissionInterface $permission): bool
    {
        return $this->repository->deleteNode($permission->getId());
    }

    /**
     * @throws RbacException
     */
    public function removeRecursively(PermissionInterface $permission): bool
    {
        return $this->repository->deleteSubtree($permission->getId());
    }

    public function hasPermission(int $permissionId, mixed $userId): bool
    {
        return $this->repository->hasPermission($permissionId, $userId);
    }
}
