<?php

namespace App\Security\Voter;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TicketVoter extends Voter
{
    public const EDIT = 'EDIT_TICKET';
    public const DELETE = 'DELETE_TICKET';
    public const CLOSE = 'CLOSE_TICKET';
    public const COMMENT = 'COMMENT_TICKET';
    public const REACT = 'REACT_TICKET';
    public const REPORT = 'REPORT_TICKET';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::CLOSE, self::COMMENT, self::REACT, self::REPORT])
            && $subject instanceof Ticket;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $authUser */
        $authUser = $token->getUser();

        /** @var Ticket $ticket */
        $ticket = $subject;

        if (!$authUser instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $authUser->getRoles())) {
            if (in_array($attribute, [self::COMMENT, self::REACT])) {
                return !$ticket->isClosed() && $ticket->isEnabled();
            }

            if (self::DELETE === $attribute) {
                return $ticket->isEnabled();
            }

            return true;
        }

        return match ($attribute) {
            self::EDIT => $ticket->getAuthor() === $authUser && !$ticket->isClosed() && $ticket->isEnabled(),
            self::COMMENT, self::REACT => !$ticket->isClosed() && $ticket->isEnabled(),
            self::REPORT => $ticket->isEnabled(),
            self::DELETE => $ticket->getAuthor() === $authUser && $ticket->isEnabled(),
            default => $ticket->getAuthor() === $authUser,
        };
    }
}
