<?php

namespace Triforce\RBACBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Triforce\RBACBundle\Repository\UserRoleRepository;

#[ORM\Entity(repositoryClass: UserRoleRepository::class)]

trait UserRoleTrait
{
    #[ORM\ManyToMany(targetEntity: RoleInterface::class, cascade:['persist', 'remove', 'refresh'])]
    #[ORM\JoinTable(name: "user_roles")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", onDelete: "cascade")]
    #[ORM\InverseJoinColumn(name: "role_id", referencedColumnName: "id", onDelete: "cascade")]
    private Collection $rbacRoles;

    public function __construct()
    {
        $this->rbacRoles = new ArrayCollection();
    }

    /**
     * @return Collection<int, RoleInterface>
     */
    public function getRbacRoles(): Collection
    {
        return $this->rbacRoles;
    }

    public function addRbacRole(RoleInterface $role): void
    {
        if (!$this->rbacRoles->contains($role)) {
            $this->rbacRoles[] = $role;
        }
    }

    public function removeRbacRole(RoleInterface $role): void
    {
        $this->rbacRoles->removeElement($role);
    }
}
