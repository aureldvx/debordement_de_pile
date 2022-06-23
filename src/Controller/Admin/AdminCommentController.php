<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route(path: '/admin/comments', name: 'admin_comments_')]
class AdminCommentController extends AbstractController
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/{uuid}/deactivate', name: 'deactivate', methods: ['PATCH'])]
    public function deactivate(Comment $comment, Request $request): RedirectResponse
    {
        $comment->setEnabled(false);
        $this->commentRepository->add($comment, true);

        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirect($this->urlGenerator->generate('public_home'));
    }
}
