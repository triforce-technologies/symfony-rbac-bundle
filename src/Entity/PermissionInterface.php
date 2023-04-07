<?php

namespace Triforce\RBACBundle\Entity;

interface PermissionInterface extends NodeInterface
{
    public function getParent(): ?PermissionInterface;

    public function setParent(PermissionInterface $parent): PermissionInterface;
}
