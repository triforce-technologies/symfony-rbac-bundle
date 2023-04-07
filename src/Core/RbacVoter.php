<?php

namespace Triforce\RBACBundle\Core;

use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RbacVoter extends Voter
{
    public function __construct(private Rbac $rbacManager) { }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof UserInterface || is_null($subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        try {
            return $this->rbacManager->hasPermission($attribute, $user->getId());
        } catch (Exception) {
            return false;
        }
    }
}
