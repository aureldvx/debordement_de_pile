<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const EDIT = 'EDIT_CATEGORY';
    public const DELETE = 'DELETE_CATEGORY';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Category;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $authUser */
        $authUser = $token->getUser();

        if (!$authUser instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $authUser->getRoles())) {
            return true;
        }

        return false;
    }
}
