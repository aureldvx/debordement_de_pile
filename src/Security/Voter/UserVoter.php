<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const CLOSE_ACCOUNT = 'CLOSE_ACCOUNT';
    public const BLOCK_USER = 'BLOCK_USER';
    public const ACTIVATE_USER = 'ACTIVATE_USER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CLOSE_ACCOUNT, self::BLOCK_USER, self::ACTIVATE_USER])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $authUser */
        $authUser = $token->getUser();

        /** @var User $targetUser */
        $targetUser = $subject;

        // if the user is anonymous, do not grant access
        if (!$authUser instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $authUser->getRoles())) {
            return true;
        }

        return $authUser === $targetUser;
    }
}
