<?php

namespace App\Controller\Admin;

use App\Repository\LoginActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/login-activity', name: 'admin_login_activity_')]
class AdminLoginActivityController extends AbstractController
{
    public function __construct(
        private readonly LoginActivityRepository $loginActivityRepository
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $paginator = $this->loginActivityRepository->getActivitiesPaginator(offset: $offset);

        return $this->render('admin/login_activity/list.html.twig', [
            'activities' => $paginator,
            'previous' => $offset - $this->loginActivityRepository::PAGINATOR_ACTIVITIES_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->loginActivityRepository::PAGINATOR_ACTIVITIES_PER_PAGE),
        ]);
    }
}
