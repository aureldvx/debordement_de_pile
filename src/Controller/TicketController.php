<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Enum\VoteType;
use App\Form\Ticket\CreateTicketType;
use App\Form\Ticket\EditTicketType;
use App\Helper\UserHelpers;
use App\Repository\CommentRepository;
use App\Repository\ReportRepository;
use App\Repository\TicketRepository;
use App\Repository\VoteRepository;
use App\ValueObject\CommentViewModel;
use App\ValueObject\TicketViewModel;
use Exception;
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
        private readonly VoteRepository $voteRepository,
        private readonly ReportRepository $reportRepository,
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

    /** @throws Exception */
    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(Ticket $ticket, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $paginator = $this->commentRepository->getCommentsPaginator(offset: $offset, ticket: $ticket);

        /** @var CommentViewModel[] $comments */
        $comments = array_map([$this, 'normalizeComment'], $paginator->getIterator()->getArrayCopy());
        $ticketVM = $this->normalizeTicket($ticket);

        return $this->render('public/ticket/show.html.twig', [
            'ticket' => $ticketVM,
            'ticket_entity' => $ticket,
            'comments' => $comments,
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

    #[Route(path: '/{slug}/close', name: 'close', methods: ['PATCH'])]
    public function close(Ticket $ticket): Response
    {
        $ticket->setClosed(true);
        $this->ticketRepository->add($ticket, true);

        return $this->redirectToRoute('tickets_show', ['slug' => $ticket->getSlug()]);
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

    private function normalizeTicket(Ticket $ticket): TicketViewModel
    {
        /** @var User $user */
        $user = $this->getUser();

        $ticketVM = new TicketViewModel();
        $ticketVM->uuid = $ticket->getUuid();
        $ticketVM->pseudo = $ticket->getAuthor()->getPseudo();
        $ticketVM->title = $ticket->getTitle();
        $ticketVM->content = $ticket->getContent();
        $ticketVM->slug = $ticket->getSlug();
        $ticketVM->createdAt = $ticket->getCreatedAt();
        $ticketVM->closed = $ticket->isClosed();
        $ticketVM->upVotesCount = count($this->voteRepository->findBy([
            'enabled' => true,
            'ticket' => $ticket,
            'type' => VoteType::UP_VOTE->value,
        ]));
        $ticketVM->downVotesCount = count($this->voteRepository->findBy([
            'enabled' => true,
            'ticket' => $ticket,
            'type' => VoteType::DOWN_VOTE->value,
        ]));
        $ticketVM->userHasReported = null !== $this->reportRepository->findOneBy([
            'enabled' => true,
            'ticket' => $ticket,
            'author' => $user,
        ]);
        $ticketVM->userHasUpvote = null !== $this->voteRepository->findOneBy([
            'enabled' => true,
            'ticket' => $ticket,
            'author' => $user,
            'type' => VoteType::UP_VOTE->value,
        ]);
        $ticketVM->userHasDownvote = null !== $this->voteRepository->findOneBy([
            'enabled' => true,
            'ticket' => $ticket,
            'author' => $user,
            'type' => VoteType::DOWN_VOTE->value,
        ]);

        return $ticketVM;
    }

    private function normalizeComment(Comment $comment): CommentViewModel
    {
        /** @var User $user */
        $user = $this->getUser();

        $commentVM = new CommentViewModel();
        $commentVM->uuid = $comment->getUuid();
        $commentVM->pseudo = UserHelpers::displayPseudo($comment->getAuthor());
        $commentVM->content = $comment->getContent();
        $commentVM->createdAt = $comment->getCreatedAt();
        $commentVM->children = array_map([$this, 'normalizeComment'], $comment->getChildren()->toArray());
        $commentVM->upVotesCount = count($this->voteRepository->findBy([
            'enabled' => true,
            'comment' => $comment,
            'type' => VoteType::UP_VOTE->value,
        ]));
        $commentVM->downVotesCount = count($this->voteRepository->findBy([
            'enabled' => true,
            'comment' => $comment,
            'type' => VoteType::DOWN_VOTE->value,
        ]));
        $commentVM->userHasReported = null !== $this->reportRepository->findOneBy([
            'enabled' => true,
            'comment' => $comment,
            'author' => $user,
        ]);
        $commentVM->userHasUpvote = null !== $this->voteRepository->findOneBy([
            'enabled' => true,
            'comment' => $comment,
            'author' => $user,
            'type' => VoteType::UP_VOTE->value,
        ]);
        $commentVM->userHasDownvote = null !== $this->voteRepository->findOneBy([
            'enabled' => true,
            'comment' => $comment,
            'author' => $user,
            'type' => VoteType::DOWN_VOTE->value,
        ]);

        return $commentVM;
    }
}
