<?php

namespace App\Controller;

use App\Controller\Trait\RequestContentValidatorTrait;
use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Vote;
use App\Enum\VoteType;
use App\Repository\CommentRepository;
use App\Repository\ReportRepository;
use App\Repository\TicketRepository;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/reactions', name: 'reactions_')]
class ReactionController extends AbstractController
{
    use RequestContentValidatorTrait;

    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly VoteRepository $voteRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly CommentRepository $commentRepository,
        private readonly Security $security,
    ) {
    }

    #[Route(path: '/vote/{uuid}', name: 'vote', methods: ['POST'])]
    public function vote(string $uuid, Request $request): RedirectResponse
    {
        if (!$this->validateSubmittedData(request: $request, security: $this->security, authorizedSubjects: ['ticket', 'comment'], authorizedTypes: ['up', 'down', 'report'])) {
            return $this->redirect($request->headers->get('referer')."#{$uuid}");
        }

        /** @var User $user */
        $user = $this->getUser();

        $type = 'up' === strval($request->get('type')) ? VoteType::UP_VOTE : VoteType::DOWN_VOTE;

        if ('ticket' === strval($request->get('subject'))) {
            /** @var Ticket|null $relatedEntity */
            $relatedEntity = $this->ticketRepository->findOneBy(['uuid' => $uuid]);
            $hasAlreadyVoted = $this->voteRepository->findOneBy(['enabled' => true, 'author' => $user, 'ticket' => $relatedEntity]);
        } else {
            /** @var Comment|null $relatedEntity */
            $relatedEntity = $this->commentRepository->findOneBy(['uuid' => $uuid]);
            $hasAlreadyVoted = $this->voteRepository->findOneBy(['enabled' => true, 'author' => $user, 'comment' => $relatedEntity]);
        }

        if (!$relatedEntity) {
            return $this->redirect($request->headers->get('referer')."#{$uuid}");
        }

        if (null !== $hasAlreadyVoted) {
            $hasAlreadyVoted->setEnabled(false);
            $this->voteRepository->add($hasAlreadyVoted, true);

            if ($hasAlreadyVoted->getType() === $type) {
                return $this->redirect($request->headers->get('referer')."#{$uuid}");
            }
        }

        $vote = new Vote();

        $vote
            ->setAuthor($user)
            ->setType($type);

        if ('ticket' === strval($request->get('subject'))) {
            /** @var Ticket $ticket */
            $ticket = $relatedEntity;
            $vote->setTicket($ticket);
        } else {
            /** @var Comment $comment */
            $comment = $relatedEntity;
            $vote->setComment($comment);
        }

        $this->voteRepository->add($vote, true);

        return $this->redirect($request->headers->get('referer')."#{$uuid}");
    }

    #[Route(path: '/report/{uuid}', name: 'report', methods: ['POST'])]
    public function report(string $uuid, Request $request): RedirectResponse
    {
        $description = strval($request->get('description'));

        if (!$this->validateSubmittedData(request: $request, security: $this->security, authorizedSubjects: ['ticket', 'comment'], authorizedTypes: ['up', 'down', 'report']) || empty($description)) {
            return $this->redirect($request->headers->get('referer')."#{$uuid}");
        }

        /** @var User $user */
        $user = $this->getUser();

        if ('ticket' === strval($request->get('subject'))) {
            /** @var Ticket|null $relatedEntity */
            $relatedEntity = $this->ticketRepository->findOneBy(['uuid' => $uuid]);
            $alreadyReported = null !== $this->reportRepository->findOneBy([
                'author' => $user,
                'ticket' => $relatedEntity,
            ]);
        } else {
            /** @var Comment|null $relatedEntity */
            $relatedEntity = $this->commentRepository->findOneBy(['uuid' => $uuid]);
            $alreadyReported = null !== $this->reportRepository->findOneBy([
                'author' => $user,
                'comment' => $relatedEntity,
            ]);
        }

        if (!$relatedEntity || $alreadyReported) {
            return $this->redirect($request->headers->get('referer')."#{$uuid}");
        }


        $report = new Report();

        $report
            ->setAuthor($user)
            ->setDescription(htmlentities(htmlspecialchars($description)));

        if ('ticket' === strval($request->get('subject'))) {
            /** @var Ticket $ticket */
            $ticket = $relatedEntity;
            $report->setTicket($ticket);
        } else {
            /** @var Comment $comment */
            $comment = $relatedEntity;
            $report->setComment($comment);
        }

        $this->reportRepository->add($report, true);

        return $this->redirect($request->headers->get('referer')."#{$uuid}");
    }
}
