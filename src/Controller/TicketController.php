<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\User;
use App\Form\Ticket\CreateTicketType;
use App\Form\Ticket\EditTicketType;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: '/t', name: 'tickets_')]
class TicketController extends AbstractController
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly CommentRepository $commentRepository,
        private readonly SluggerInterface $slugger,
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $paginator = $this->ticketRepository->getTicketsPaginator(offset: $offset, withClosed: false);

        return $this->render('public/ticket/list.html.twig', [
            'tickets' => $paginator,
            'previous' => $offset - $this->ticketRepository::PAGINATOR_TICKETS_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->ticketRepository::PAGINATOR_TICKETS_PER_PAGE),
        ]);
    }

    #[Route(path: '/new', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        $ticket = new Ticket();

        $form = $this
            ->createForm(CreateTicketType::class, $ticket)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $ticket
                ->setSlug($this->slugger->slug($ticket->getTitle()))
                ->setAuthor($user);

            $this->ticketRepository->add($ticket, true);

            return $this->redirectToRoute('tickets_show', ['slug' => $ticket->getSlug()]);
        }

        return $this->renderForm('public/ticket/create.html.twig', [
            'ticket_form' => $form,
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(Ticket $ticket, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $paginator = $this->commentRepository->getCommentsPaginator(offset: $offset, ticket: $ticket);

        return $this->render('public/ticket/show.html.twig', [
            'ticket' => $ticket,
            'comments' => $paginator,
            'previous' => $offset - $this->commentRepository::PAGINATOR_COMMENTS_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->commentRepository::PAGINATOR_COMMENTS_PER_PAGE),
        ]);
    }

    #[Route(path: '/{slug}/edit', name: 'edit', methods: ['GET', 'PATCH'])]
    public function edit(Ticket $ticket, Request $request): Response
    {
        if (!$this->isGranted('EDIT_TICKET', $ticket)) {
            return $this->redirectToRoute('tickets_list');
        }

        $form = $this
            ->createForm(EditTicketType::class, $ticket)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setSlug($this->slugger->slug($ticket->getTitle()));

            $this->ticketRepository->add($ticket, true);

            return $this->redirectToRoute('tickets_show', ['slug' => $ticket->getSlug()]);
        }

        return $this->renderForm('public/ticket/edit.html.twig', [
            'ticket_form' => $form,
            'ticket' => $ticket,
        ]);
    }

    #[Route(path: '/{slug}', name: 'delete', methods: ['DELETE'])]
    public function delete(Ticket $ticket): Response
    {
        if ($this->isGranted('DELETE_TICKET', $ticket)) {
            $ticket->setClosed(true);
            $ticket->setEnabled(false);
            $this->ticketRepository->add($ticket, true);
        }

        return $this->redirectToRoute('tickets_list');
    }
}
