<?php

namespace Triforce\RBACBundle\Core\Manager;

use Triforce\RBACBundle\Entity\PermissionInterface;
use Triforce\RBACBundle\Exception\RbacException;
use Triforce\RBACBundle\Repository\PermissionRepository;

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
