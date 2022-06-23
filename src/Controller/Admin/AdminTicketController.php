<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(path: '/admin/tickets', name: 'admin_tickets_')]
class AdminTicketController extends AbstractController
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $resolvedOnly = $request->query->getAlnum('state', 'active');
        $paginator = $this->ticketRepository->getPaginator(offset: $offset, enabled: 'active' === $resolvedOnly, closed: 'closed' === $resolvedOnly);

        return $this->render('admin/ticket/list.html.twig', [
            'tickets' => $paginator,
            'previous' => $offset - $this->ticketRepository::ADMIN_MAX_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->ticketRepository::ADMIN_MAX_PER_PAGE),
        ]);
    }

    #[Route(path: '/{uuid}/deactivate', name: 'deactivate', methods: ['PATCH'])]
    public function deactivate(Ticket $ticket, Request $request): RedirectResponse
    {
        $ticket->setEnabled(false);
        $ticket->setClosed(true);
        $this->ticketRepository->add($ticket, true);

        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirect($this->urlGenerator->generate('public_home'));
    }
}
