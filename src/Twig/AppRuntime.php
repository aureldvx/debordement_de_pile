<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\Ticket;
use App\Entity\User;
use App\Helper\UserHelpers;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    public function displayPseudo(User $user): string
    {
        return UserHelpers::displayPseudo($user);
    }

    public function getCommentsCountForTicket(Ticket $ticket): int
    {
        return count($this->commentRepository->findByTicket($ticket));
    }

    public function getTicketsCountForCategory(Category $category): int
    {
        return count($this->ticketRepository->findByCategory($category));
    }
}
