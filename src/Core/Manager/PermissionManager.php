<?php

namespace SymfonyRbacBundle\Core\Manager;

use SymfonyRbacBundle\Entity\RoleInterface;
use SymfonyRbacBundle\Exception\RbacException;
use SymfonyRbacBundle\Repository\PermissionRepository;
use SymfonyRbacBundle\Entity\PermissionInterface;

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
