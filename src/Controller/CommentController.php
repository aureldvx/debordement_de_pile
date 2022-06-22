<?php

namespace App\Controller;

use App\Controller\Trait\RequestContentValidatorTrait;
use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/comments', name: 'comments_')]
class CommentController extends AbstractController
{
    use RequestContentValidatorTrait;

    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly CommentRepository $commentRepository,
        private readonly Security $security,
    ) {
    }

    #[Route('/new', name: 'create', methods: ['POST'])]
    public function create(Request $request): RedirectResponse
    {
        $content = strval($request->get('content'));
        $parentUuid = strval($request->get('parent_uuid'));
        $ticketUuid = strval($request->get('ticket_uuid'));

        if (!$this->validateSubmittedData(request: $request, security: $this->security, authorizedSubjects: ['ticket', 'comment']) || empty($content)) {
            if (!empty($parentUuid)) {
                return $this->redirect($request->headers->get('referer')."#{$parentUuid}");
            }

            return $this->redirect($request->headers->get('referer')."#{$ticketUuid}");
        }

        /** @var User $user */
        $user = $this->getUser();

        $subject = $request->get('subject');

        /** @var Ticket|null $ticket */
        $ticket = $this->ticketRepository->findOneBy(['uuid' => $ticketUuid]);

        if (!$ticket) {
            return $this->redirect($request->headers->get('referer')."#{$ticketUuid}");
        }

        /** @var Comment|null $relatedEntity */
        $relatedEntity = null;

        if ('comment' === $subject && !empty($parentUuid)) {
            $relatedEntity = $this->commentRepository->findOneBy(['uuid' => $parentUuid]);
        }

        $comment = new Comment();

        $comment
            ->setAuthor($user)
            ->setTicket($ticket)
            ->setParent($relatedEntity)
            ->setContent(htmlentities(htmlspecialchars($content)));

        $this->commentRepository->add($comment, true);

        return $this->redirect($request->headers->get('referer')."#{$comment->getUuid()}");
    }
}
