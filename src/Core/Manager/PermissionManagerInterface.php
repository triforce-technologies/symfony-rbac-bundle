<?php

namespace Triforce\RBACBundle\Core\Manager;

use Triforce\RBACBundle\Entity\PermissionInterface;
use Triforce\RBACBundle\Exception\RbacPermissionNotFoundException;

interface PermissionManagerInterface extends NodeManagerInterface
{
    /**
     * Remove permission and attach all the sub-permission to the parent
     *
     * @param PermissionInterface $permission
     *
     * @throws RbacPermissionNotFoundException
     * @return boolean
     */
    public function remove(PermissionInterface $permission): bool;

    /**
     * Remove Permission and all sub-permissions from system
     *
     * @param PermissionInterface $permission
     *
     * @throws RbacPermissionNotFoundException
     * @return boolean
     */
    public function removeRecursively(PermissionInterface $permission): bool;

    /**
     * check if a user has the permission or not
     *
     * @param int   $permissionId
     * @param mixed $userId
     *
     * @return bool
     */
    public function hasPermission(int $permissionId, mixed $userId): bool;
}
