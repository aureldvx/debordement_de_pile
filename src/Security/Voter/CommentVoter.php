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
    public const COMMENT = 'COMMENT_COMMENT';
    public const REACT = 'REACT_TICKET';
    public const REPORT = 'REPORT_TICKET';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::COMMENT, self::REACT, self::REPORT])
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
            if (in_array($attribute, [self::COMMENT, self::REACT])) {
                return !$comment->getTicket()->isClosed() && $comment->getTicket()->isEnabled();
            }

            return true;
        }

        return match ($attribute) {
            self::EDIT => $comment->getAuthor() === $authUser && !$comment->getTicket()->isClosed() && $comment->getTicket()->isEnabled(),
            self::COMMENT, self::REACT => !$comment->getTicket()->isClosed() && $comment->getTicket()->isEnabled(),
            self::REPORT => $comment->getTicket()->isEnabled(),
            default => $comment->getAuthor() === $authUser,
        };
    }
}
