<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const EDIT = 'EDIT_COMMENT';
    public const DELETE = 'DELETE_COMMENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $authUser */
        $authUser = $token->getUser();

        /** @var Comment $comment */
        $comment = $subject;

        if (!$authUser instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $authUser->getRoles())) {
            return true;
        }

        return $comment->getAuthor() === $authUser;
    }
}
