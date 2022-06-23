<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\User\EditPersonalDataType;
use App\Helper\DateTimeHelpers;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/users', name: 'admin_users_')]
class AdminUserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $manager,
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $resolvedOnly = $request->query->getAlnum('state', 'active');
        $paginator = $this->userRepository->getPaginator(offset: $offset, enabled: 'active' === $resolvedOnly, blocked: 'blocked' === $resolvedOnly);

        return $this->render('admin/user/list.html.twig', [
            'users' => $paginator,
            'previous' => $offset - $this->userRepository::ADMIN_MAX_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->userRepository::ADMIN_MAX_PER_PAGE),
        ]);
    }

    #[Route(path: '/{uuid}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit', methods: ['GET', 'PATCH'])]
    public function edit(User $user, Request $request): Response
    {
        $form = $this
            ->createForm(EditPersonalDataType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->add($user, true);

            return $this->redirectToRoute('admin_users_show', ['uuid' => $user->getUuid()]);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'user_form' => $form,
        ]);
    }

    #[Route(path: '/{uuid}/ban', name: 'ban', methods: ['PATCH'])]
    public function ban(User $user): Response
    {
        $user->setBlockedAt(DateTimeHelpers::createImmutable());
        $this->userRepository->add($user, true);

        return $this->redirectToRoute('admin_users_list', ['state' => 'blocked']);
    }

    #[Route(path: '/{uuid}/unban', name: 'unban', methods: ['PATCH'])]
    public function unban(User $user): Response
    {
        $user->setBlockedAt(null);
        $this->userRepository->add($user, true);

        return $this->redirectToRoute('admin_users_list', ['state' => 'active']);
    }

    #[Route(path: '/{uuid}/disable', name: 'disable', methods: ['PATCH'])]
    public function disable(User $user, Request $request): Response
    {
        $this->switchUserState($user, $request, false);

        return $this->redirectToRoute('admin_users_list', ['state' => 'closed']);
    }

    #[Route(path: '/{uuid}/enable', name: 'enable', methods: ['PATCH'])]
    public function enable(User $user, Request $request): Response
    {
        $this->switchUserState($user, $request, true);

        return $this->redirectToRoute('admin_users_list', ['state' => 'active']);
    }

    private function switchUserState(User $user, Request $request, bool $state): void
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user->setEnabled($state);
            $this->userRepository->add($user, true);
        }
    }
}
